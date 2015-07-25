<?php
namespace Locomo;
class Model_Flr_Tmp extends Model_Flr
{
	public static $_table_name = 'lcm_flrs_tmp';

	/**
	 * $_has_many
	 */
	protected static $_has_many = array(
		'permission_usergroup' => array(
			'key_from' => 'id',
			'model_to' => '\Model_Flr_Usergroup_Tmp',
			'key_to' => 'flr_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		),
		'permission_user' => array(
			'key_from' => 'id',
			'model_to' => '\Model_Flr_User_Tmp',
			'key_to' => 'flr_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		)
	);
}
