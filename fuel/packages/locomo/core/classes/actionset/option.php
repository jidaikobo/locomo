<?php
namespace Locomo;
class Actionset_Option extends Actionset
{
	/**
	 * actionset_add_testdata()
	 */
	public static function actionset_add_testdata($module, $obj, $get_authed_url)
	{
		$url = '';
		$usergroup_ids = \User\Controller_User::$userinfo['usergroup_ids'];

		//ルート管理者のみ
		if(in_array(-2, $usergroup_ids)):
			$url = $module."/add_testdata";
		endif;

		//インデクスでしか表示しない
		$url = (substr(\Uri::string(), -12) == '/index_admin') ? $url : '';

		$retvals = array(
			'url'           => $url,
			'confirm'       => true,
			'action_name'   => 'テストデータの追加',
			'menu_str'      => 'テストデータ追加',
			'dependencies'  => array(
				'add_testdata',
			)
		);
		return $retvals;
	}
}
