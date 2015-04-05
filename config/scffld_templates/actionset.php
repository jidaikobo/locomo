<?php
namespace XXX;
class Actionset_XXX extends \Actionset
{
//	use \Actionset_Traits_Revision;
//	use \Actionset_Traits_Wrkflw;
//	use \Actionset_Traits_Testdata;

	/*
	(str)  realm         メニューの表示位置。省略するとbase
	(arr)  urls          メニューに表示するリンク先。\Controller_Foo/actionの形式
	(bool) show_at_top   モジュール／コントローラトップに表示するかどうか
	(str)  action_name   ACL設定画面などで用いる
	(str)  explanation   モジュール先頭画面等で用いる説明文
	(int)  order         表示順
	(arr)  dependencies  このアクションセットが依存するアクション。\Controller_Foo/actionの形式
	*/

	/**
	 * actionset_index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_index_admin($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_create($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_view($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_edit()
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_edit($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_delete()
	 */
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_delete($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_undelete()
	 */
	public static function actionset_undelete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_undelete($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_sample_action()
	 */
	public static function _actionset_sample_action($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'edit' && $id)
		{
			$urls = array(array($controller.DS."sample_action/".$id, 'STR'));
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'STR',
			'explanation'  => 'STR',
			'show_at_top'  => true,
			'order'        => 10,
			'dependencies' => array(
				$controller.'/sample_action',
			)
		);
		return $retvals;
	}
}
