<?php
namespace Acl;
class Model_Acl extends \Orm\Model
{
	protected static $_table_name = 'acls';

	protected static $_properties = array(
		'id',
		'controller',
		'action',
		'slug',
		'usergroup_id',
		'user_id',
	);

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
	 * get_mod_or_ctrl()
	 * Locomo配下にあるacl対象コントローラ／モジュールの取得
	 */
	public static function get_mod_or_ctrl()
	{
		//モジュールディレクトリを走査し、$locomoのメンバ変数を持っている物を洗い出す
		$retvals = array();
		foreach(array_keys(\Module::get_exists()) as $module)
		{
			if( ! $controllers = \Module::get_controllers($module)) continue;// module which not has controllers
			\Module::loaded($module) or \Module::load($module);
			foreach($controllers as $controller)
			{
				$mod_ctrl = \Inflector::path_to_ctrl($controller);
				if( ! property_exists($mod_ctrl, 'locomo')) continue;
				if(array_key_exists($module, $retvals)) continue; // already exists
				$retvals[$module] = \Arr::get($mod_ctrl::$locomo, 'nicename') ?: $mod_ctrl ; 
			}
		}

		//classを走査し、$locomoのメンバ変数を持っている物を洗い出す
		foreach(array_keys(\Inflector::dir_to_ctrl(APPPATH.'classes/controller')) as $ctrl):
			if( ! property_exists($ctrl, 'locomo')) continue;
			$retvals[$ctrl] = \Arr::get($ctrl::$locomo, 'nicename') ?: $ctrl ; 
		endforeach;

		return array('none' => '選択してください') + $retvals;
	}

}
