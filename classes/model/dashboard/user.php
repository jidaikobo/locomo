<?php
namespace Locomo;
class Model_Dashboard_User extends Model_Base
{
	protected static $_table_name = 'lcm_usrs';
	protected static $_properties = array(
		'id'
	);

	/**
	 * $_has_many
	 */
	protected static $_has_many = array(
		'dashboard' => array(
			'key_from' => 'id',
			'model_to' => '\Model_Dashboard',
			'key_to' => 'user_id',
			'cascade_save' => true,
			'cascade_delete' => false,
//			'conditions' => array('where'=>array(array('position',1))),
		),
	);

	/**
	 * find()
	 * to find admins who are not in users table
	 */
	public static function find($id = NULL, array $options = array())
	{
		$retvals = parent::find($id, $options);
		if ($retvals) return $retvals;

		// admin
		return \Model_Dashboard_Admin::find('first', array('where' => array(array('username'=>\Auth::get('username')))));
	}
}
