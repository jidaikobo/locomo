<?php
namespace Locomo_Core;

class Bulk {

	protected $name = 'bulk';

	protected $forms = array();

	protected $models = array();

	public function __construct ($name) {
		$this->name = $name;
	}

	public static function forge($name = 'bulk_form') {
		return new static($name);
	}


	public function name() {
		return $this->name;
	}



	public function add_model($model) {


		if(is_array($model)) {
			foreach ($model as $model_obj) {
				$this->add_model($model_obj);
			}

		} else {
			if ($model->is_new()) {
				$key = 'bulk_new_' . count($this->models);
			} else {
				$key = 'bulk_' . $model[$model::primary_key()[0]];
			}
			$this->models[$key] = $model;
			if (method_exists($model, 'bulk_form_definition')) {
				$this->forms[$key] = $model->bulk_form_definition($key, $model); // todo id factory nessesity?
			} elseif (method_exists($model, 'form_definition')) {
				$this->forms[$key] = $model->form_definition($key, $model); // todo id factory nessesity?
			} else {
				$this->forms[$key] = \Fieldset::forge($key)->add_model($model)->populate($model);
			}
			
			if ($model->is_new()) {
				$this->forms[$key]->add('_deleted', '削除', array('type' => 'checkbox', 'value' => 1, 'disabled' => true))->set_template("\t\t\t\t<td>{field}{label}{error_msg}</td>");
			} else {
				$this->forms[$key]->add('_deleted', '削除', array('type' => 'checkbox', 'value' => 1))->set_template("\t\t\t\t<td>{field}{label}{error_msg}</td>");
			}

			$this->forms[$key]->set_input_name_array($key);
		}

		if (! $model instanceof \Orm\Model) return false;

		return $this;
	}



	public function __get($name) {
		if ($name == 'models') return $this->models;
	}

	/*
	 */
	public function build() {
		$output = '<thead><tr>';

		$fst_obj = reset($this->forms);
		foreach ($fst_obj->field() as $f) {
			if (is_null ($f->template)) {
				$output .=  '<th>' . $f->label . '</th>';
			} else {
				$temp = str_replace('td>', 'th>', $f->template);
				$temp = str_replace('{fields}', '', $temp);
				$temp = str_replace('{field}', $f->label, $temp);
				$temp = str_replace('{label}','',  str_replace('{error_msg}','', $temp));
				$output .= $temp;
			}
		}
		$output .= '</tr><thead>';

		foreach($this->forms as $form) {


			$form->set_config('form_template', "\t\t\t<tr>\n{fields}\n\t\t\t</tr>\n");
			foreach ($form->field() as $field) {
				if ($field->type == 'checkbox') {
					is_null($field->template) and $field->set_template("\t\t\t\t<td>{fields}\n\t\t\t\t{field} {label}<br />\n{fields}{error_msg}\n\t\t\t</td>");
				} else {
					is_null($field->template) and $field->set_template("\t\t\t\t<td>{field}{error_msg}</td>");
				}
			}

			$output .= $form->build();
		}

		$output = str_replace('{fields}', $output, "\n\t\t\n\t\t<table>\n{fields}\n\t\t</table>\n\t\t\n");
		return $output;
	}


	/*
	 * @return validate
	 */
	public function save($use_transaction = true, $validation = true) {

		$validated = array();

		// transaction start
		if ($use_transaction)
		{
			$db = \Database_Connection::instance();
			$db->start_transaction();
		}

		try
		{
			foreach ($this->models as $key => $model) {

				// 削除 new は無視する
				if (isset(\Input::post($key)['_deleted']) and !$model->is_new()) {
					$model->delete();
					$this->forms[$key]->field('_deleted')->set_value(1, true);

				// save
				} else {

					// 新規で全ての field が空なら continue
					$judge = array_filter(\Input::post($key));
					if (empty($judge) and $model->is_new()) continue;

					$mm_fields = array();
					// mm field
					foreach ($model::relations() as $rel) {
						if ( $rel instanceof \Orm\ManyMany) $mm_fields[] = $rel->name;
					}

					// mm 有り 個別にフィールド設定 & populate $ validation
					if ($mm_fields) {

						foreach ($mm_fields as $mm_field) {
							if (isset(\Input::post($key)[$mm_field])) {

								// セットされているフィールドで来ていないもの
								$setted_unset_objs = \Arr::filter_keys($this->models[$key]->{$mm_field}, \Input::post($key)[$mm_field], true);
								foreach ($setted_unset_objs as $unset_key => $vv) {
									unset($this->models[$key]->{$mm_field}[$unset_key]);
								}


								// セットされているもので来ているもの
								$unseted_ids = array_flip(\Arr::filter_keys(array_flip(\Input::post($key)[$mm_field]), array_keys($this->models[$key]->{$mm_field}), true));
								if (!empty($unseted_ids)) {
									foreach ($unseted_ids as $unseted_id) {
										$mm_model = $model::relations($mm_field)->model_to;
										$this->models[$key]->{$mm_field}[$unseted_id] = $mm_model::find($unseted_id);
									}
								}

								// Fieldset_Field なので populate じゃなく set_value
								$this->forms[$key]->field($mm_field)->set_value(\Input::post($key)[$mm_field]);

							// 何も飛んでこなかったとき、form に存在していれば 全て unset する
							} else {
								if ($this->forms[$key]->field($mm_field) instanceof \Fieldset_Field) unset($this->models[$key]->{$mm_field});
								// Fieldset_Field なので populate じゃなく set_value
								$this->forms[$key]->field($mm_field)->set_value(array());
							}


						}

						// mm 以外をセットする
						$this->models[$key]->set(\Arr::filter_keys(\Input::post($key), $mm_fields, true));
						$model->save(null, false);

					// mm なし
					} else {
						$model->set(\Input::post($key));
					}


					if ($this->forms[$key]->populate(\Input::post())->validation()->run(\Input::post())) {
						$model->save(null, false);
					} else {
						if ($validation) $validated[] = false;
					}

				} // endif savw


/*


						if (isset(\Input::post($key)[$rel->name])) {
							$mm_model = $rel->model_to;

							// セットされているフィールドで来ていないもの
							$setted_unset_objs = \Arr::filter_keys($this->models[$key]->{$rel->name}, \Input::post($key)[$rel->name], true);
							foreach ($setted_unset_objs as $unset_key => $vv) {
								unset($this->models[$key]->{$unset_key});
							}

							// セットされているもので来ているもの
							$unseted_ids = array_flip(\Arr::filter_keys(array_flip(\Input::post($key)[$rel->name]), array_keys($this->models[$key]->{$rel->name}), true));
							if (!empty($unseted_ids)) {
								foreach ($unseted_ids as $unseted_id) {
									// var_dump($mm_model::find($unseted_id));
									$this->models[$key]->{$unseted_id} = $mm_model::find($unseted_id);
								}
							}

							// Fieldset_Field なので populate じゃなく set_value
							$this->forms[$key]->field($rel->name)->set_value($rel);

						// 何も飛んでこなかったとき、form に存在していれば 全て unset する
						} else {
							if ($this->forms[$key]->field($rel->name) instanceof \Fieldset_Field) unset($this->models[$key]->{$rel->name});
						}

 */













			}
		} // -> try

		// if catch error => rollback
		catch (\Exception $e)
		{
			$use_transaction and $db->rollback_transaction();
			throw $e;
		}

		if (!in_array(false, $validated)) {
			// commit
			$use_transaction and $db->commit_transaction();
			return true;
		} else {
			// rollback
			$use_transaction and $db->rollback_transaction();
			return false;
		}
	}

	public static function connection($writeable = false)
	{
		$class = get_called_class();

		if ($writeable and property_exists($class, '_write_connection'))
		{
			return static::$_write_connection;
		}

		return property_exists($class, '_connection') ? static::$_connection : null;
	}

}

