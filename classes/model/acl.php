<?php
namespace Locomo;
class Model_Acl extends \Orm\Model
{
	protected static $_table_name = 'lcm_acls';

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
	 * @param str   $controller
	 * @param array $actions
	 *
	 * @return  array
	 */
	public static function judge_set($controller, $actions)
	{
		// 比較用にすべてのアクションセットを取得
		// staticへの格納用にいったん取得までとする
		static $all = array();
		if ( ! $all)
		{
			foreach (\Util::get_mod_or_ctrl() as $ctrl => $v)
			{
				$module = \Inflector::get_modulename($controller, $default = '');
				if ($module)
				{
					\Module::loaded($module) or \Module::load($module);
					$all = array_merge($all, \Actionset::get_module_actionset($module));
				}

				$all[$ctrl] = \Actionset::get_actionset($ctrl);
			}
		}

		//アクションセットの条件を満たすものを抽出
		$results = array();
		foreach($all as $ctrl => $actionsets)
		{
			foreach($actionsets as $actionset_name => $v)
			{
				if ( ! isset($v['dependencies']) || ! is_array($v['dependencies'])) continue;
				if ($controller != $ctrl) continue;
				if ( ! array_diff($v['dependencies'], $actions))
				{
					$results[] = $actionset_name;
				}
			}
		}
		return $results;
	}

	/**
	 * get_usergroups()
	 */
	public static function get_usergroups()
	{
		$usergroups = array('none' => '選択してください', 0 => 'ゲスト', '-10' => 'ログインユーザすべて');
		$usergroups += \Model_Usrgrp::get_options(
			array(
				'where' => array(
					array('is_available', '=', true),
					array('customgroup_uid', 'is', null)
				),
				'order_by' => array('seq' => 'ASC', 'name' => 'ASC')
			),
			'name'
		) ?: array();
		return $usergroups;
	}

	/**
	 * get_mod_or_ctrl()
	 * Locomo配下にあるacl対象コントローラ／モジュールの取得
	 */
	public static function get_mod_or_ctrl()
	{
		$retvals = array();
		foreach(\Util::get_mod_or_ctrl() as $k => $v)
		{
			if (\Arr::get($v, 'no_acl')) continue;
			$retvals[\Inflector::ctrl_to_safestr($k)] = \Arr::get($v, 'nicename');
		}

		return array('none' => '選択してください') + $retvals;
	}

}
