<?php
namespace XXX;
class Presenter_XXX_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

/*
		// modify template source
		$field_template = \Config::get('form')['field_template'];

		// add field
		$options = \Model_Name::find_options('name', array('where' => array(array('category', 'NAME'))));
		$form->add_after(
			'objname',
			'NAME',
			array('type' => 'checkbox', 'options' => $options),
			array(),
			'user_type')
			->set_value(array_keys($obj->objname));

		// set_tabular_form
		$tabular_form = \Fieldset::forge('relation_name')->set_tabular_form('Model_Name', 'relation_name', $obj, 2);
		$form->add_after($tabular_form, 'name', array(), array(), 'field_name');
*/

		return $form;
	}
}
