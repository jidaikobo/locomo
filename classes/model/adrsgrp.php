<?php
class Model_Adrsgrp extends \Model_Base
{
//	use \Model_Traits_Wrkflw;

	protected static $_table_name = 'lcm_adrs_groups';

	public static $_options = array();

	// $_conditions
	protected static $_conditions = array(
		'where' => array(
			array('is_available', true)
		),
		'order_by' => array('seq' => 'acs'),
	);

	protected static $_properties =
	array (
		'id',
		'name' => array(
			'label' => 'グループ名',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
			'validation' => 
			array (
				'required',
				'max_length' => 
				array (
					255,
				),
			),
		),
		'description',
		'seq',
		'is_available',
	);

	protected static $_has_many = array(
		'address' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Adrs',
			'key_to' => 'group_id',
			'cascade_save' => false,
			'cascade_delete' => false
		)
	);

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory = 'usergroup', $obj = null)
	{
		$form = \Fieldset::forge($factory, \Config::get('form'));

		//id
		$form->add(
			'id',
			'ID',
			array('type' => 'text', 'disabled' => 'disabled', 'size' => 2)
		)
		->set_value(@$obj->id);

		//name
		$form->add(
				'name',
				'グループ名',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->name)
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('unique', "lcm_usrgrps.name.".@$obj->id);

		//description
		$form->add(
				'description',
				'説明',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->description)
			->add_rule('max_length', 255);

		//order
		$form->add(
				'seq',
				'表示順',
				array('type' => 'text', 'size' => 5)
			)
			->set_value(@$obj->seq)
			->add_rule('valid_string', array('numeric'));

		//is_available
		$form->add(
				'is_available',
				'使用中',
				array('type' => 'select', 'options' => array('0' => '未使用', '1' => '使用中'), 'default' => 0)
			)
			->set_value(@$obj->is_available);

		return $form;
	}
}
