<?php
namespace Kontiki;
abstract class Model_Acl extends \Kontiki\Model
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
			if( ! is_array($v['dependencies'])) continue;
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
		$controllers = array('none' => '選択してください');

		foreach(\Config::get('module_paths') as $path):
			foreach (glob($path.'*') as $dirname):
				if( ! is_dir($dirname)) continue;
				//config
				$config = \Config::load($dirname.'/config/'.basename($dirname).'.php', $use_default_name = true, $reload = true);
				if( ! $config) continue;

				//admin_onlyだったらaclの対象外
				if(@$config['is_admin_only']) continue;

				//アクションセットのないコントローラは対象外にする
				$controller = basename($dirname);
				if( ! self::get_controller_actionset($controller, $is_owner)) continue;

				$controllers[$controller] = $config['nicename'] ;
			endforeach;
		endforeach;

		return $controllers;
	}

	/**
	 * get_controller_actionset()
	 * acl対象コントローラのアクションセット取得
	 */
	public static function get_controller_actionset($controller = null, $is_owner = false)
	{
		$class = '\\'.ucfirst($controller).'\\Controller_'.ucfirst($controller);

		//モジュールの存在確認
		foreach(\Config::get('module_paths') as $path):
			foreach (glob($path.'*') as $dirname):
				if( ! is_dir($dirname)) continue;
				$ctlfile = $dirname.DS.'classes/controller/'.basename($dirname).'.php';
				if( ! file_exists($ctlfile)) continue;
				require_once($ctlfile);
			endforeach;
		endforeach;
		if( ! class_exists($class)) return false;

		//アクションセットの取得
		$request = \Request::forge();
		$controller_obj = new $class($request);
		$controller_obj->set_actionset($controller);

		if($is_owner):
			return $controller_obj::$actionset_owner;
		else:
			return $controller_obj::$actionset;
		endif;
	}
}
