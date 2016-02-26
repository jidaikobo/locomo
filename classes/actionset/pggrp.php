<?php
namespace Locomo;
class Actionset_Pggrp extends \Actionset_Base
{
//	use \Actionset_Traits_Testdata;
//	use \Actionset_Traits_Wrkflw;
//	use \Actionset_Traits_Revision;

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
	 * actionset_pg()
	 */
	public static function actionset_pg($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => array(array("pg/index_admin", 'ページ管理')) ,
			'show_at_top'  => false,
			'order'        => 100,
			'dependencies' => array(
				'\\Controller_Pg/create',
			)
		);
		return $retvals;
	}

	/**
	 * actionset_create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::create($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::view($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_edit()
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::edit($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_delete()
	 */
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::delete($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_undelete()
	 */
	public static function actionset_undelete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::undelete($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_purge_confirm()
	 */
	public static function actionset_purge_confirm($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::purge_confirm($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$ret = \Actionset_Base::index_admin($controller, $obj, $id, $urls);
		$ret['show_at_top'] = true;
		$ret['explanation']  = 'カテゴリ管理一覧';
		$ret['order']  = -1;
		return $ret;
	}

	/**
	 * actionset_index_deleted()
	 */
	public static function actionset_index_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_deleted($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_index_yet()
	 */
	public static function actionset_index_yet($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_yet($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_sample_action()
	 */
	public static function _actionset_sample_action($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'edit' && $id)
		{
			$urls = array(array($controller."/sample_action/".$id, 'STR'));
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
