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
			$controller.'::action_index_admin',
			$controller.'::action_create',
			$controller.'::action_edit',
			$controller.'::action_view',
			$controller.'::action_delete',
			$controller.'::action_index_deleted',
			$controller.'::action_view_deleted',
			$controller.'::action_edit_deleted',
			$controller.'::action_undelete',
			$controller.'::action_confirm_delete',
		);
		\Arr::set($retvals, 'dependencies', $actions);
		\Arr::set($retvals, 'action_name', 'カスタムユーザグループへの基本権限');
		\Arr::set($retvals, 'acl_exp', 'カスタムユーザグループへの基本権限です。');
		return $retvals;
	}

	/**
	 * create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_create($controller, $obj, $id, $urls);
	}

	/**
	 * view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_view($controller, $obj, $id, $urls);
	}

	/**
	 * edit()
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_edit($controller, $obj, $id, $urls);
	}

	/**
	 * edit_deleted()
	 */
	public static function actionset_edit_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_edit_deleted($controller, $obj, $id, $urls);
	}
	
	/**
	 * delete()
	 */
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_delete($controller, $obj, $id, $urls);
	}

	/**
	 * undelete()
	 */
	public static function actionset_undelete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_undelete($controller, $obj, $id, $urls);
	}

	/**
	 * delete_deleted()
	 */
	public static function actionset_delete_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::actionset_delete_deleted($controller, $obj, $id, $urls);
	}
}
