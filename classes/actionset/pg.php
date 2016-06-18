<?php
namespace Locomo;
class Actionset_Pg extends \Actionset_Base
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
		$ret = \Actionset_Base::view($controller, $obj, $id, $urls);
		if (\Request::main()->action == 'edit' && $obj && isset($obj->path))
		{
			$ret['urls'] = array(array(\Lang::get_lang().DS.$obj->path, '閲覧'));
		}
		return $ret;
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
		return \Actionset_Base::index_admin($controller, $obj, $id, $urls);
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
	 * actionset_index_expired()
	 */
	public static function actionset_index_expired($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_expired($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_index_invisible()
	 */
	public static function actionset_index_invisible($controller, $obj = null, $id = null, $urls = array())
	{
		$ret = \Actionset_Base::index_invisible($controller, $obj, $id, $urls);
		$ret['urls'] = array(array($controller.DS.'index_invisible', '一般非公開項目 ('.$ret['count'].')'));
		return $ret;
	}

	/**
	 * actionset_index_unavailable()
	 */
	public static function actionset_index_unavailable($controller, $obj = null, $id = null, $urls = array())
	{
		$ret = \Actionset_Base::index_unavailable($controller, $obj, $id, $urls);
		$ret['urls'] = array(array($controller.DS.'index_unavailable', '下書き項目 ('.$ret['count'].')'));
		return $ret;
	}

	/**
	 * actionset_index_all()
	 */
	public static function actionset_index_all($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_all($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_pggrp()
	 */
	public static function actionset_pggrp($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => array(array("pggrp/index_admin", 'カテゴリ管理')) ,
			'explanation'  => 'カテゴリ管理です',
			'show_at_top'  => true,
			'order'        => 100,
			'dependencies' => array(
				'\\Controller_Pggrp/create',
			)
		);
		return $retvals;
	}

	/**
	 * actionset_pgfl()
	 */
	public static function actionset_pgfl($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => array(array("pgfl/index_admin", '添付ファイル管理')) ,
			'explanation'  => '添付ファイル管理です',
			'show_at_top'  => true,
			'order'        => 110,
			'dependencies' => array(
				'\\Controller_Pgfl/create',
			)
		);
		return $retvals;
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
