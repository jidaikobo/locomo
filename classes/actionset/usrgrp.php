<?php
namespace Locomo;
class Actionset_Usrgrp extends \Actionset
{
	// traits
	use \Actionset_Traits_Revision;

	/**
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_admin($controller, $obj, $id);
		$actions = array(
			'\Controller_Usrgrp/index_admin',
			'\Controller_Usrgrp/create',
			'\Controller_Usrgrp/edit',
			'\Controller_Usrgrp/view',
			'\Controller_Usrgrp/delete',
			'\Controller_Usrgrp/undelete',
			'\Controller_Usrgrp/view_revision',
			'\Controller_Usrgrp/index_revision',
			'\Controller_Usrgrp/each_index_revision',
		);
		\Arr::set($retvals, 'dependencies', $actions);
		return $retvals;
	}

	/**
	 * actionset_index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_admin($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_purge_confirm()
	 */
	public static function actionset_purge_confirm($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::purge_confirm($controller, $obj, $id, $urls);
	}


	/**
	 * actionset_create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::create($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_edit()
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::edit($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::view($controller, $obj, $id, $urls);
	}
}
