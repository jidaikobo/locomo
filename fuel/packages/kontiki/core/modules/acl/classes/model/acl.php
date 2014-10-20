<?php
namespace Kontiki_Core_Module\Acl;
class Model_Acl extends \Orm\Model
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
		$usergroups = array('none' => '選択してください', 0 => 'ゲスト');
		$usergroups += \Option\Model_Option::get_options('usergroups');
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
		static $checked_controller = array();

		//なぜか二度読むのであとで見直し
		static $retval = array();
		static $retval4owner = array();
		if($is_owner && $retval4owner) return $retval4owner;
		if( ! $is_owner && $retval) return $retval;

		//すでにclassが存在している場合はオーバライドされているのでfalse
		$class = '\\'.ucfirst($controller).'\\Controller_'.ucfirst($controller);
		if(in_array($class, $checked_controller)) return false;
		$checked_controller[] = $class;

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
/*
$controller_obj = \Kontiki\Util::get_valid_controller_name($controller);
echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">' ;
var_dump( $controller ) ;
var_dump( $controller_obj::$actionset ) ;
echo '</textarea>' ;
*/
		$request = \Request::forge();
		$controller_obj = new $class($request);
		$controller_obj->set_actionset($controller);

		if($is_owner):
			return $retval4owner = $controller_obj::$actionset_owner;
		else:
			return $retval =  $controller_obj::$actionset;
		endif;
	}
}
