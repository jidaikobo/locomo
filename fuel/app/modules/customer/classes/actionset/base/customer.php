<?php
namespace Customer;
class Actionset_Base_Customer extends \Actionset_Base
{
//	use \Revision\Traits_Actionset_Base_Revision;
//	use \Workflow\Traits_Actionset_Base_Workflow;

	/*
	(bool) is_admin_only 管理者のみに許された行為。ACL設定画面に表示されなくなる
	(str)  url           メニューに表示するリンク先。配列、入れ子の可能。
	(str)  action_name   ACL設定画面で用いる
	(str)  explanation   ACL設定画面で用いる説明文
	(str)  menu_str      メニューで用いる
	(bool) confirm       確認用のJavaScriptを表示する場合はtrue
	(arr)  dependencies  このアクションセットが依存するアクション。前後のスラッシュはつけないこと
	*/

	/**
	 * actionset_sample_action()
	 * to use remove first underscore at the function name
	 */
	public static function _actionset_sample_action($module, $obj, $get_authed_url)
	{
		if($get_authed_url):
			$url_str = $module."/sample_action" ;
			$url = self::check_auth($module, 'sample_action') ? $url_str : '' ;
		endif;

		$retvals = array(
			'is_admin_only' => false,
			'url'           => @$url ?: '' ,
			'action_name'   => 'sample_action',
			'explanation'   => 'explanation of sample_action',
			'menu_str'      => 'menustr of sample_action',
			'dependencies' => array(
				'sample_action',
			)
		);
		return $retvals;
	}
}
