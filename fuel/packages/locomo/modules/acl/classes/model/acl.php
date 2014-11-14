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
		$usergroups = array('none' => '選択してください', 0 => 'ゲスト', '-10' => 'ログインユーザすべて');
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
		$all = \Util::get_mod_or_ctrl();
		$retvals = array();
		foreach($all as $k => $v):
			if(\Arr::get($v, 'is_for_admin')) continue;
			if( ! \Arr::get($v, 'show_at_menu')) continue;
			$retvals[$k] = \Arr::get($v, 'nicename');
		endforeach;
		return array('none' => '選択してください') + $retvals;
	}

}
