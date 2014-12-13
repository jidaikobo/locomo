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
		'seq',
		'is_available',
		'deleted_at',
	);

	protected static $_many_many = array(
		'user' => array(
			'key_from' => 'id',
			'key_through_from' => 'group_id',
			'table_through' => 'user_usergroups',
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
				'ユーザグループ名',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->name)
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('unique', "usergroups.name.".@$obj->id);

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