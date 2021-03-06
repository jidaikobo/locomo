<?php
namespace Locomo;
class Model_Usrgrp extends \Model_Base
{
	protected static $_table_name = 'lcm_usrgrps';

	// $_conditions
	protected static $_conditions = array(
		'where' => array(
			array('is_available', true)
		),
		'order_by' => array('seq' => 'acs'),
	);

	// $_properties
	protected static $_properties = array(
		'id' => array(
			'label' => 'ID',
			'form' => array('type' => 'text', 'disabled' => 'disabled', 'size' => 2),
		),
		'name' => array(
			'lcm_role' => 'subject',
			'label' => 'ユーザグループ名',
			'form' => array('type' => 'text', 'size' => 20),
			'validation' => array(
				'required',
				'max_length' => array(50),
			)
		),
		'description' => array(
			'label' => '説明',
			'form' => array('type' => 'textarea', 'class' => 'textarea'),
		),
		'seq' => array(
			'label' => '表示順',
			'form' => array('type' => 'text', 'size' => '3'),
			'validation' => array(
				'valid_string' => array('numeric'),
			)
		),
		'is_available' => array(
			'label' => '使用中',
			'form' => array('type' => 'select', 'options' => array(0 => '使用していない', 1 => '使用している')),
			'default' => 1,
		),
		'is_for_acl' => array(
			'label' => '権限用',
			'form' => array('type' => 'select', 'options' => array(0 => '通常', 1 => '権限用グループ')),
			'default' => 0,
		),
	);

	// relations
	protected static $_many_many = array(
		'user' => array(
			'key_from' => 'id',
			'key_through_from' => 'group_id',
			'table_through' => 'lcm_usrs_usrgrps',
			'key_through_to' => 'user_id',
			'model_to' => 'Model_Usr',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'usergroup' => array(
			'key_from' => 'id',
			'key_through_from' => 'group_id_from',
			'table_through' => 'lcm_usrgrps_usrgrps',
			'key_through_to' => 'group_id_to',
			'model_to' => 'Model_Usrgrp',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);

	// $_observers
	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save'),
		),
	);

	public static function _init()
	{
		if (\Request::main()->action == 'bulk' ) {
			static::$_properties['name']['validation'] = array(
				'max_length' => array(50),
			);
		}
	}
}
