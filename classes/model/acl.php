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
	 * @param str   $actions
	 * @param array $actionsets
	 *
	 * @return  array
	 */
	public static function judge_set($actions, $actionsets)
	{
		// 比較用にすべてのアクションセットを取得
		// staticへの格納用にいったん取得までとする
		static $all = array();
		if ( ! $all)
		{
			foreach (\Util::get_mod_or_ctrl() as $ctrl => $v)
			{
				$all[$ctrl] = \Actionset::get_actionset($ctrl);
			}
		}

		//アクションセットの条件を満たすものを抽出
		$results = array();
		foreach($all as $ctrl => $v)
		{
			foreach($v as $realm => $actionsets)
			{
				foreach($actionsets as $actionset_name => $v)
				{
					if ( ! isset($v['dependencies']) || ! is_array($v['dependencies'])) continue;
					if ( ! array_diff($v['dependencies'], $actions))
					{
						$results[] = $actionset_name;
					}
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
		$usergroups += \Model_Usrgrp::get_options(array('order_by' => array('seq' => 'ASC', 'name' => 'ASC') ), 'name') ?: array();
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
		$users += \Model_Usr::get_options($options, $label = 'username');

		return $users;
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
