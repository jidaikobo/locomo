<?php
namespace Acl;
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
		$val = \Locomo_Validation::forge($factory);
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
			if( ! isset($v['dependencies']) || ! is_array($v['dependencies'])) continue;
			$dependencies = array_map(array('\\Auth_Acl_Locomoacl','_parse_conditions'), $v['dependencies']);
			$dependencies = array_map('serialize', $dependencies);
			if( ! array_diff($dependencies, $actions)){
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
		$opt = \User\Model_Usergroup::get_option_options('usergroup');
		$usergroups = \User\Model_Usergroup::get_options($opt['option'], $opt['label']);
		$usergroups = array('none' => '選択してください', 0 => 'ゲスト');
		$usergroups += \User\Model_Usergroup::get_options($opt['option'], $opt['label']);
		return $usergroups;
	}

	/**
	 * get_users()
	 */
	public static function get_users()
	{
		$options['select'][] = 'username';
//		$options['where'][] = array('is_visible', true);
		$options['where'][] = array('created_at', '<', date('Y-m-d H:i:s'));
		$options['where'][] = array(
			array('expired_at', '>', date('Y-m-d H:i:s')),
			'or' => array(
				array('expired_at', 'is', null),
			)
		);
		$users = array('none' => '選択してください');
		$users += \User\Model_User::get_options($options, $label = 'username');

		return $users;
	}

	/**
	 * get_controllers()
	 * configで指定されたacl対象コントローラの取得
	 */
	public static function get_controllers($is_owner = false)
	{
		$controllers = array();
		foreach(\Util::get_all_configs() as $module => $config):
			if(\Arr::get($config, 'is_admin_only', false)) continue;
			if( ! \Arr::get($config, 'actionset_classes', false)) continue;
			if( ! $controller = \Arr::get($config, 'main_controller', false)) continue;
			$controllers[$controller] = $config['nicename'];
		endforeach;
		return array('none' => '選択してください') + $controllers;
	}

}
