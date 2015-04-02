<?php
namespace XXX;
class Actionset_XXX extends \Actionset_Base
{
//	use \Actionset_Traits_Revision;
//	use \Actionset_Traits_Wrkflw;
//	use \Actionset_Traits_Testdata;

	/*
	(str)  realm         メニューの表示位置。デフォルトはbase
	(arr)  urls          メニューに表示するリンク先。\Controller_Foo/actionの形式
	(bool) show_at_top   モジュール／コントローラトップに表示するかどうか
	(str)  action_name   ACL設定画面などで用いる
	(str)  explanation   モジュール先頭画面等で用いる説明文
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
			$urls = array(array($controller.DS."sample_action/".$id, '閲覧'));
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'sample_action',
			'explanation'  => 'explanation of sample_action',
			'show_at_top'  => true,
			'order'        => 10,
			'dependencies' => array(
				$controller.'/sample_action',
			)
		);
		return $retvals;
	}
}
