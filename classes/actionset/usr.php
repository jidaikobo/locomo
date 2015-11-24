<?php
namespace Locomo;
class Actionset_Usr extends \Actionset_Base
{
	// traits
	use \Actionset_Traits_Revision;
	use \Actionset_Traits_Testdata;

	/**
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_admin($controller, $obj, $id);
		$actions = array(
			'\Controller_Usr/index_admin',
			'\Controller_Usr/index_deleted',
			'\Controller_Usr/index_yet',
			'\Controller_Usr/index_expired',
			'\Controller_Usr/index_invisible',
			'\Controller_Usr/index_all',
			'\Controller_Usr/create',
			'\Controller_Usr/edit',
			'\Controller_Usr/view',
			'\Controller_Usr/delete',
			'\Controller_Usr/confirm_delete',
			'\Controller_Usr/view_deleted',
			'\Controller_Usr/undelete',
			'\Controller_Usr/view_revision',
			'\Controller_Usr/index_revision',
			'\Controller_Usr/each_index_revision',
		);
		\Arr::set($retvals, 'dependencies', $actions);
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
	 * actionset_edit_other()
	 */
	public static function actionset_edit_other($controller, $obj = null, $id = null, $urls = array())
	{
		$retval = \Actionset_Base::edit($controller, $obj, $id, $urls);

		$retval['action_name'] = '編集（自分以外）';
		$retval['explanation'] = '編集（自分以外）';
		$retval['dependencies'][] = '\Controller_Usr/edit_other';

		return $retval;
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
		return \Actionset_Base::index_invisible($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_index_all()
	 */
	public static function actionset_index_all($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_all($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_reset_paswd()
	 */
	public static function actionset_reset_paswd($controller, $obj = null, $id = null, $urls = array())
	{
		if (in_array(\Request::main()->action, ['edit', 'view']) && $id)
		{
			$urls = array(array($controller.DS."reset_paswd/".$id, 'パスワードリセット'));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'order'        => 100,
		);

		return $retvals;
	}

	/**
	 * actionset_bulk_reset_paswd()
	 */
	public static function actionset_bulk_reset_paswd($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Auth::is_root())
		{
			$urls = array(array($controller.DS."bulk_reset_paswd", '一括パスワードリセット'));
		}

		$retvals = array(
			'realm'        => 'option',
			'urls'         => $urls,
			'order'        => 100,
		);

		return $retvals;
	}
}
