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
		$options['select'][] = 'user_name';
		$options['where'][] = array('created_at', '<=', date('Y-m-d'));
		$options['where'][] = array('expired_at', '>=', date('Y-m-d'));
		$users = array('none' => '選択してください');
		$users += \User\Model_User::get_options($options, $label = 'user_name');
		return $users;
	}

	/**
	 * get_controllers()
	 * configで指定されたacl対象コントローラの取得（とりあえずモジュール形式だけ）
	 */
	public static function get_controllers($realm = '')
	{
		//nicename
		$modules = array_keys(\Actionset::get_actionset(null, $realm));
		$controllers = array();
		foreach($modules as $module):
			$config = \Config::load(\Actionset::get_modules()[$module].DS.'config'.DS.$module.'.php', $module);
			$controllers[$module] = $config['nicename'];
		endforeach;
		return array('none' => '選択してください') + $controllers;
	}

}
