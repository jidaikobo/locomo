<?php
namespace Locomo;
class Actionset_Usrgrp_Custom extends \Actionset
{
	/**
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_admin($controller, $obj, $id);
		$actions = array(
			$controller.'/index_admin',
			$controller.'/index_unavailable',
			$controller.'/create',
			$controller.'/edit',
			$controller.'/view',
			$controller.'/delete',
			$controller.'/index_deleted',
			$controller.'/view_deleted',
			$controller.'/undelete',
			$controller.'/confirm_delete',
		);
		\Arr::set($retvals, 'dependencies', $actions);
		return $retvals;
	}

	/**
	 * create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::create($controller, $obj, $id, $urls);
	}

	/**
	 * view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::view($controller, $obj, $id, $urls);
	}

	/**
	 * edit()
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::edit($controller, $obj, $id, $urls);
	}

	/**
	 * delete()
	 */
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::delete($controller, $obj, $id, $urls);
	}

	/**
	 * undelete()
	 */
	public static function actionset_undelete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::undelete($controller, $obj, $id, $urls);
	}

	/**
	 * purge_confirm()
	 */
	public static function actionset_purge_confirm($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::purge_confirm($controller, $obj, $id, $urls);
	}

	/**
	 * index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_admin($controller, $obj, $id, $urls);
	}

	/**
	 * index_unavailable()
	 */
	public static function actionset_index_unavailable($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_unavailable($controller, $obj, $id, $urls);
	}
}
