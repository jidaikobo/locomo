<?php
class Presenter_Sys_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

		// default
		$create = 3;
		$num = count($obj->dashboard);
		$defaults = array();
		if ($num === 0)
		{
			$defaults = \Config::get('default_dashboard') ?: array();
			if ( ! $defaults) continue;
			$create += count($defaults);
		}
		// actions
		$fieldset = \Fieldset::forge('dashboard');
		$fieldset->set_tabular_form('Model_Dashboard', 'dashboard', $obj, $create);

		if ($num === 0) {
			$i = 0;
			foreach ($defaults as $default)
			{
				$new_row = $fieldset->field('dashboard_new_' . $i);
				//var_dump($new_row);
				foreach ($default as $k => $v) {
					if ($field = $new_row->field('dashboard_new[' . $i . '][' . $k . ']')) {
						$field->set_value($v);
					}
				}
				$i++;
			}
		}

		$form->add_before($fieldset, 'ダッシュボードウィジェット', array(), array(), 'submit');

		return $form;
	}
}
