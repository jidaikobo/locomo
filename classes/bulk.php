<?php
namespace Locomo;
class Bulk
{
	protected $forms = array();
	protected $models = array();
	public static $_presenter = null;
	protected static $_enable_deleted = false;

	/*
	 * __construct()
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/*
	 * forge()
	 */
	public static function forge($name = 'bulk_form')
	{
		return new static($name);
	}

	/*
	 * add_model()
	 */
	public function add_model($model, $add_delete_field = true)
	//, $define_function = null) {
	{
		if (is_array($model))
		{
			foreach ($model as $model_obj)
			{
				$this->add_model($model_obj, $add_delete_field);
			}

		} else {
			if ($model->is_new())
			{
				$key = 'bulk_new_' . count($this->models);
			} else {
				$key = 'bulk_' . $model[$model::primary_key()[0]];
			}

			$this->models[$key] = $model;
			$presenter = \Presenter::forge(static::$_presenter);
			$this->forms[$key] = $presenter::bulk($key, $model);

			if ($add_delete_field) $this->add_delete_field($this->forms[$key], $model);

			$this->forms[$key]->set_input_name_array($key);
		}

		if (! $model instanceof \Orm\Model) return false;

		return $this;
	}

	/*
	 * add_delete_field()
	 * $field の対象の $model を判定して、適切な削除フィールドを付ける
	 */
	private function add_delete_field($field, $model)
	{
		if ($model->is_new()) {

			$field->add('_deleted', '削除', array('type' => 'checkbox', 'value' => 1, 'disabled' => true))->set_template("\t\t\t\t<td>{field}{label}{error_msg}</td>"); // disable

			if (method_exists($model, 'get_filter_status'))
			{
				!$model::get_filter_status() and $field->add('_restore', '復活・完全削除', array('type' => 'select', 'options' => array(0 => '== 未削除項目 ==',),'disabled' => true,)); // disable
			}

		} else {

			// softdelete
			if (method_exists($model, 'get_filter_status')) {
				if (is_null($model->{$model::soft_delete_property('deleted_field')})) { // 削除済みでない
					$field->add('_deleted', '削除', array('type' => 'checkbox', 'value' => 1))->set_template("\t\t\t\t<td>{field}{label}{error_msg}</td>");
					if (method_exists($model, 'get_filter_status'))
					{
						!$model::get_filter_status() and $field->add('_restore', '復活・完全削除', array('type' => 'select', 'options' => array(0 => '== 未削除項目 ==',),'disabled' => true,)); // disable
					}
				} else { // 削除済み
					$field->add('_deleted', '削除', array('type' => 'checkbox', 'value' => 1, 'disabled' => true,))->set_template("\t\t\t\t<td>{field}{label}{error_msg}</td>"); // disable
					if (method_exists($model, 'get_filter_status') && !$model::get_filter_status()) {
						$field->add('_restore', '復活・完全削除', array(
							'type' => 'select',
							'options' => array(
								0 => '= 選択 =',
								1 => '復活させる',
								2 => '完全に削除する',
							),
						));
					}
				}
			} else {
				$field->add('_deleted', '削除', array('type' => 'checkbox', 'value' => 1,))->set_template("\t\t\t\t<td>{field}{label}{error_msg}</td>"); // disable
			}
		}
	}


	/*
	 * __get()
	 */
	public function __get($name)
	{
		if ($name == 'models') return $this->models;
	}

	/*
	 * build()
	 */
	public function build($header = true, $footer = false)
	{
		if ( ! $this->forms) return '';


		$header_str = '';
		// header
		if ($header)
		{
			if (is_string($header))
			{
				$header_str = $header;
			}
			else
			{
				$header_str .= '<thead><tr>';
				$fst_obj = reset($this->forms);
				foreach ($fst_obj->field() as $f)
				{
					if ($f->type === false  OR $f->type === 'hidden' OR $f->type === 'submit') continue;
					$header_str .=  '<th>' . $f->label . '</th>';
					/*
					if (is_null ($f->template))
					{
						$header_str .=  '<th>' . $f->label . '</th>';
					} else {
						$temp = str_replace('td>', 'th>', $f->template);
						$temp = str_replace('{fields}', '', $temp);
						$temp = str_replace('{field}', $f->label, $temp);
						$temp = str_replace('{label}','',  str_replace('{error_msg}','', $temp));
						$header_str .= $temp;
					}
					 */
				}
				$header_str .= '</tr><thead>';
			}
		}

		// footer
		$footer_str = '';
		if ($footer)
		{
			if (is_string($footer))
			{
				$footer_str = $footer;
			}
			else
			{
				$footer_str = str_replace('thead', 'tfoot', $header_str);
			}
		}


		// body
		$body = '';
		foreach($this->forms as $form)
		{
			$form->set_config('form_template', "\t\t\t<tr>\n{fields}\n\t\t\t</tr>\n");
			foreach ($form->field() as $field) {
				if ($field->type === 'submit')
				{
					$form->delete('submit');
				}

				if ($field->type == 'checkbox' OR $field->type == 'radio')
				{
					is_null($field->template) and $field->set_template("\t\t\t\t<td>{fields}\n\t\t\t\t{field} {label}\n{fields}{error_msg}\n\t\t\t</td>");
				} else {
					is_null($field->template) and $field->set_template("\t\t\t\t<td>{field}{error_msg}</td>");
				}
				// no required rules on this row
				$field->delete_rule('required', false)->delete_rule('required_with', false);
			}

			$body .= $form->build();
		}

		$output = $header_str.$body.$footer_str;

		$output = str_replace('{fields}', $output, "\n\t\t\n\t\t<table class=\"tbl datatable\">\n{fields}\n\t\t</table>\n\t\t\n"); // todo template
		return $output;
	}

	/*
	 * save()
	 * @return validate
	 */
	public function save($use_transaction = true, $validation = true)
	{
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


	/*
	 * set_define_function()
	 */
	public static function set_define_function($name)
	{
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
	 * @return  string
	 */
	public function __toString()
	{
		return $this->build();
	}
}
