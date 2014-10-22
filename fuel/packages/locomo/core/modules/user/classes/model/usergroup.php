<?php
namespace Locomo_Core_Module\User;
class Model_Usergroup extends \Locomo\Model_Base
{
	use \Option\Model_Option;

	protected static $_table_name = 'usergroups';

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
			'model_to' => '\Locomo_Core_Module\User\Model_User',
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
	protected static $_option_options = array(
		'usergroup' => array(
			'nicename' => 'ユーザグループ',
			'label'    => 'name',
			'order_field' => 'order',
			'option'   => 
				array(
					'select' => array('name'),
					'where'  => array(
						array('is_available', '1'),
					),
					'order_by'  => array(
						array('order', 'ASC'),
					)
				)
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
	public static function form_definition($factory, $obj = null, $id = '')
	{
		$form = \Fieldset::forge('form', \Config::get('form'));

		//user_name
		$form->add(
				'name',
				'ユーザグループ名',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->name)
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('unique', "usergroups.name.{$id}");

		//display_name
		$form->add(
				'description',
				'説明',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->description)
			->add_rule('max_length', 255);

		//password
		$form->add(
				'order',
				'表示順',
				array('type' => 'text', 'size' => 5)
			)
			->set_value(@$obj->description)
			->add_rule('valid_string', array('numeric'));

		//confirm_password
		$form->add(
				'is_available',
				'使用中',
				array('type' => 'checkbox')
			)
			->set_value(@$obj->is_available);

		return $form;
	}

}
