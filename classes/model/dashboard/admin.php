<?php
namespace Locomo;
class Model_Dashboard_Admin extends Model_Dashboard_User
{
	protected static $_table_name = 'lcm_usr_admins';
	protected static $_primary_key = array('user_id');
	protected static $_properties = array(
		'id' => array(
			'form' => array(
				'type' => false
			)
		),
		'user_id'
	);

	/**
	 * $_has_many
	 */
	protected static $_has_many = array(
		'dashboard' => array(
			'key_from' => 'user_id',
			'model_to' => '\Model_Dashboard',
			'key_to' => 'user_id',
			'cascade_save' => true,
			'cascade_delete' => false,
//			'conditions' => array('where'=>array(array('position',1))),
		),
	);
}
