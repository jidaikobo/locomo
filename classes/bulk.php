<?php
namespace Locomo;

class Bulk {

	protected $forms = array();

	protected $models = array();

	protected static $_define_function = null;

	protected static $_enable_deleted = false;

	public function __construct ($name) {
		$this->name = $name;
	}

	public static function forge($name = 'bulk_form') {
		return new static($name);
	}

	public function add_model($model) {//, $define_function = null) {

		if (is_array($model)) {
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
			if (method_exists($model, static::$_define_function)) {
				$this->forms[$key] = $model->{static::$_define_function}($key, $model);
			} elseif (method_exists($model, 'bulk_definition')) {
				$this->forms[$key] = $model::bulk_definition($key, $model);
				// if ($this->forms[$key]->field('submit')) $this->forms[$key]->delete('submit');
			} elseif (method_exists($model, 'form_definition')) {
				$this->forms[$key] = $model::form_definition($key, $model);
			} else {
				$this->forms[$key] = \Fieldset::forge($key)->add_model($model)->populate($model);
			}
			
			if ($model->is_new()) {

				$this->forms[$key]->add('_deleted', '削除', array('type' => 'checkbox', 'value' => 1, 'disabled' => true))->set_template("\t\t\t\t<td>{field}{label}{error_msg}</td>"); // disable
				!$model::get_filter_status() and $this->forms[$key]->add('_restore', '復活・完全削除', array('type' => 'select', 'options' => array(0 => '== 未削除項目 ==',),'disabled' => true,)); // disable
			} else {
				if (is_null($model->{$model::soft_delete_property('deleted_field')})) { // 削除済みでない
					$this->forms[$key]->add('_deleted', '削除', array('type' => 'checkbox', 'value' => 1))->set_template("\t\t\t\t<td>{field}{label}{error_msg}</td>");
					!$model::get_filter_status() and $this->forms[$key]->add('_restore', '復活・完全削除', array('type' => 'select', 'options' => array(0 => '== 未削除項目 ==',),'disabled' => true,)); // disable
				} else { // 削除済み
					$this->forms[$key]->add('_deleted', '削除', array('type' => 'checkbox', 'value' => 1, 'disabled' => true,))->set_template("\t\t\t\t<td>{field}{label}{error_msg}</td>"); // disable
					if (!$model::get_filter_status()) {
						$this->forms[$key]->add('_restore', '復活・完全削除', array(
							'type' => 'select',
							'options' => array(
								0 => '= 選択 =',
								1 => '復活させる',
								2 => '完全に削除する',
							),
						));
					}

				}
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
			if ($f->type === false  OR $f->type === 'hidden' OR $f->type === 'submit') continue;
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
				if ($field->type === 'submit') {
					$form->delete('submit');
				}
				if ($field->type == 'checkbox' OR $field->type == 'radio') {
					is_null($field->template) and $field->set_template("\t\t\t\t<td>{fields}\n\t\t\t\t{field} {label}\n{fields}{error_msg}\n\t\t\t</td>");
				} else {
					is_null($field->template) and $field->set_template("\t\t\t\t<td>{field}{error_msg}</td>");
				}
				// no required rules on this row
				$field->delete_rule('required', false)->delete_rule('required_with', false);

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

				} elseif (isset(\Input::post($key)['_restore']) and \Input::post($key)['_restore'] != 0 and !$model->is_new()) {

					if (\Input::post($key)['_restore'] == 1) {
						$model->restore();
					} elseif (\Input::post($key)['_restore'] == 2) {
						$model->purge();
					}

					$this->forms[$key]->field('_restore')->set_value(1, true);

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
							if ($this->forms[$key]->field($mm_field)) { // form にセットされているか
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


						}

						// mm 以外をセットする
						$this->models[$key]->set(\Arr::filter_keys(\Input::post($key), $mm_fields, true));

					// mm なし
					} else {
						$model->set(\Input::post($key));
					}



					if ($this->forms[$key]->populate(\Input::post())->validation()->run(\Input::post())) {
						$model->save(null, false);
					} else {
						if ($validation) $validated[] = false;
					}

				}
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


	public static function set_define_function($name) {
		if ($name) static::$_define_function = $name;
	}


	/*
	public static function disable_deleted() {
		static::$_disable_dleted = true;
	}

	public static function enable_deleted() {
		static::$_disable_dleted = false;
	}
	 */


	/**
	 * Magic method toString that will build this as a form
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->build();
	}

}

