<?php
namespace Locomo;
class Model_Auth_Usr extends Model_Base
{
	/**
	 * vals
	 */
	protected static $_table_name = 'lcm_usrs';

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'username',
		'display_name',
		'email',
		'main_usergroup_id',
		'password',
		'is_visible',
		'last_login_at',
		'expired_at',
		'created_at',
		'login_hash',
		'activation_key',
		'profile_fields',
		'updated_at',
		'deleted_at',
	//	'creator_id',
	//	'updater_id',
	);

	/**
	 * relations
	 */
	protected static $_many_many = array(
		'usergroup' => array(
			'key_from' => 'id',
			'key_through_from' => 'user_id',
			'table_through' => 'lcm_usrs_usrgrps',
			'key_through_to' => 'group_id',
			'model_to' => '\Model_Auth_Usrgrp',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);
	protected static $_belongs_to = array(
		'main_usergroup' => array(
			'key_from' => 'main_usergroup_id',
			'model_to' => '\Model_Auth_Usrgrp',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);
}
