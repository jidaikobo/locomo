<?php
namespace Kontiki;

class Model_Acl_Abstract extends \Kontiki\Model
{
	protected static $_table_name = 'acls';

	protected static $_properties = array(
		'controller',
		'action',
		'usergroup_id',
		'user_id',
	);

	public static function validate($factory, $id = '')
	{
		$val = \Kontiki_Validation::forge($factory);
		$val->add_field('controller', 'controller name', 'required|max_length[50]');
		$val->add_field('action', 'action name', 'required|max_length[50]');
		$val->add_field('usergroup_id', 'usergroup_id', "required|numeric");
		$val->add_field('user_id', 'user_id', "required|numeric");
		return $val;
	}

	/**
	 * get_usergroups()
	 * 
	 */
	public static function get_usergroups()
	{
		$usergroups_model = \Usergroup\Model_Usergroup::forge();
		$args = array('type' => 'array');
		$usergroups = array('none' => '選択してください', 0 => 'ゲスト');
		$usergroups += \Arr::assoc_to_keyval($usergroups_model->find_items($args)->results, 'id', 'usergroup_name');
		return $usergroups;
	}

	/**
	 * get_users()
	 * 
	 */
	public static function get_users()
	{
		$users_model = \User\Model_User::forge();
		$args = array('type' => 'array');
		$users = array('none' => '選択してください');
		$users += \Arr::assoc_to_keyval($users_model->find_items($args)->results, 'id', 'user_name');
		return $users;
	}

	/**
	 * get_controllers()
	 * 
	 */
	public static function get_controllers()
	{
		$modules_from_config = \Config::get('acl');
		$modules = array('none' => '選択してください');
		foreach($modules_from_config as $module):
			$class = '\\'.ucfirst($module).'\\Controller_'.ucfirst($module);
			if( ! class_exists($class)) continue;
			$modules[$module] = $class::$nicename ;
		endforeach;
		return $modules;
	}
}
