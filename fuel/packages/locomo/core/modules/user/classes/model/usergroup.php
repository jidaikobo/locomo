<?php
namespace User;
class Model_Usergroup extends \Locomo\Model_Base
{
	protected static $_table_name = 'usergroups';
	public static $_subject_field_name = 'name';

	protected static $_properties = array(
		'id',
		'name',
		'description',
		'order',
		'is_available',
		'deleted_at',
	);

	protected static $_many_many = array(
		'user' => array(
			'key_from' => 'id',
			'key_through_from' => 'group_id',
			'table_through' => 'usergroups_r',
			'key_through_to' => 'user_id',
			'model_to' => '\User\Model_User',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);

	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	/*
	 * option setting
	 */
	public static $_option_options = array(
		'usergroup' => array(
			'nicename'        => 'ユーザグループ',
			'label'           => 'name',
			'order_field'     => 'order',
			'template'        => '',//use bulk
			'form_definition' => 'bulk_definition',
			'option' => array(
				'select' => array('name'),
				'where'  => array(
					array('is_available', '1'),
				),
				'order_by'  => array(
					array('order', 'ASC'),
				)
			),
			'range' => array(
				'select' => array('name'),
				'where'  => array(
					array('id', '>', '0'),
				),
				'order_by'  => array(
					array('order', 'ASC'),
				)
			)
		)
	);

	protected static $_observers = array(
		'Revision\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save'),
		),
	);

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory, $obj = null, $id = '')
	{
		$form = \Fieldset::forge($factory, \Config::get('form'));

		//name
		$form->add(
				'name',
				'ユーザグループ名',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->name)
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('unique', "usergroups.name.{$id}");

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
				'order',
				'表示順',
				array('type' => 'text', 'size' => 5)
			)
			->set_value(@$obj->order)
			->add_rule('valid_string', array('numeric'));

		//is_available
		$form->add(
				'is_available',
				'使用中',
				array('type' => 'checkbox')
			)
			->set_value(@$obj->is_available);

		return $form;
	}

	public static function bulk_definition($factory, $obj = null, $id = '')
	{
		$form = \Fieldset::forge($factory, \Config::get('form'));

		//id
		$form->add(
			'id',
			'ID',
			array('type' => 'text', 'disabled' => 'disabled', 'size' => 2)
		)
		->set_value(@$obj->id);

		//user_name
		$form->add(
				'name',
				'ユーザグループ名',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->name)
			//->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('unique', "usergroups.name.{$obj->id}");

		//display_name
		$form->add(
				'description',
				'説明',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->description)
			->add_rule('max_length', 255);

		$form->add(
				'order',
				'表示順',
				array('type' => 'text', 'size' => 5)
			)
			->set_value(@$obj->order)
			->add_rule('valid_string', array('numeric'));

		$form->add(
				'is_available',
				'使用中',
				array('type' => 'select', 'options' => array('0' => '未使用', '1' => '使用中'), 'default' => 0)
			)
			->set_value($obj->is_available, true);

		return $form;

	}

}
