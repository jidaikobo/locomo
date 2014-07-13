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

	/**
	 * validate()
	 */
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
	 * judge_set()
	 *
	 * @param str   $actions
	 * @param array $actionsets
	 *
	 * @return  array
	 */
	public static function judge_set($actions, $actionsets)
	{
		//アクションセットの条件を満たすものを抽出
		$results = array();
		foreach($actionsets as $actionset_name => $v){
			if( ! array_diff($v['dependencies'], $actions)){
				$results[] = $actionset_name;
			};
		}
		return $results;
	}

	/**
	 * get_usergroups()
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
	 * configで指定されたacl対象コントローラの取得（とりあえずモジュール形式だけ）
	 */
	public static function get_controllers($is_owner = false)
	{
		$controllers_from_config = \Config::get('modules');
		$controllers = array('none' => '選択してください');
		foreach($controllers_from_config as $controller => $settings):
			//admin_onlyだったらaclの対象外
			if($settings['is_admin_only']) continue;

			//アクションセットのないコントローラは対象外にする
			if( ! self::get_controller_actionset($controller, $is_owner)) continue;

			$controllers[$controller] = $settings['nicename'] ;
		endforeach;
		return $controllers;
	}

	/**
	 * get_controller_actionset()
	 * packageconfigで指定されたacl対象コントローラの取得
	 */
	public static function get_controller_actionset($controller = null, $is_owner = false)
	{
		$class = '\\'.ucfirst($controller).'\\Controller_'.ucfirst($controller);
		if( ! class_exists($class)) return false;

		//アクションセットの取得
		$request = \Request::forge();
		$controller_obj = new $class($request);
		$controller_obj->set_actionset();

		if($is_owner):
			return $controller_obj::$actionset_owner;
		else:
			return $controller_obj::$actionset;
		endif;
	}
}
