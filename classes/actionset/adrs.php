<?php
class Actionset_Adrs extends \Actionset_Base
{
//	use \Actionset_Traits_Revision;
//	use \Actionset_Traits_Wrkflw;
//	use \Actionset_Traits_Testdata;

	/*
	(str)  realm         メニューの表示位置。デフォルトはbase
	(arr)  urls          メニューに表示するリンク先
	(bool) show_at_top   モジュール／コントローラトップに表示するかどうか
	(str)  action_name   ACL設定画面などで用いる
	(str)  explanation   モジュール先頭画面等で用いる説明文
	(str)  acl_exp       ACL設定画面などで用いる説明文
	(int)  order         表示順
	(arr)  dependencies  このアクションセットが依存するアクション
	*/

	/**
	 * actionset_sample_action()
	 * to use remove first underscore at the function name
	 */
	public static function _actionset_sample_action($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'edit' && $id):
			$actions = array(array($controller.DS."sample_action/".$id, '閲覧'));
			$urls = static::generate_urls($controller.'::action_sample_action', $actions, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'sample_action',
			'show_at_top'  => true,
			'explanation'  => 'explanation of sample_action',
			'acl_exp'      => 'explanation of sample_action for acl',
			'order'        => 10,
			'dependencies' => array(
				$controller.'::action_sample_action',
			)
		);
		return $retvals;
	}

	/**
	 * actionset_edit_adrsgrp()
	 */
	public static function actionset_edit_adrsgrp($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(
			array($controller.DS."edit_adrsgrp/", 'グループの設定'),
			array($controller.DS."edit_adrsgrp/?create=1", 'グループの新規作成'),
		);
		$urls = static::generate_urls($controller.'::action_edit_adrsgrp', $actions, ['create']);

		$retvals = array(
			'realm'        => 'option' ,
			'urls'         => $urls ,
			'action_name'  => 'グループの設定',
			'show_at_top'  => true,
			'explanation'  => 'アドレス帳のグループ設定です。',
			'acl_exp'      => 'アドレス帳のグループ設定権限です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.'::action_edit_adrsgrp',
			)
		);
		return $retvals;
	}
}
