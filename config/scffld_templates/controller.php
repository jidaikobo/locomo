<?php
namespace XXX;
class Controller_XXX extends \Locomo\Controller_Base
{
	// traits
//	use \Controller_Traits_Testdata;
//	use \Controller_Traits_Wrkflw;
//	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'nicename' => '###NICENAME###', // for human's name
		'explanation' => '###NICENAME###のコントローラです', // use at admin/admin/home
		'main_action' => 'index_admin', // main action
		'main_action_name' => '管理一覧', // main action's name
		'main_action_explanation' => '###NICENAME###のトップです。', // explanation of top page
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order' => 10,   // order of appearance
		'is_for_admin' => false, // true: place it admin's menu instead of normal menu
		'no_acl' => false, // true: admin's action. it will not appear at acl.
		'widgets' => array(
		),
	);

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		parent::index_admin();
	}

	/**
	 * action_index_yet()
	 */
	public function action_index_yet()
	{
		parent::index_yet();
	}

	/**
	 * action_index_expired()
	 */
	public function action_index_expired()
	{
		parent::index_expired();
	}

	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible()
	{
		parent::index_invisible();
	}

	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted()
	{
		parent::index_deleted();
	}

	/*
	 * action_index_all()
	 */
	public function action_index_all()
	{
		parent::index_all();
	}

	/**
	 * action_index_widget()
	 */
	public function action_index_widget()
	{
		parent::index_widget(func_get_args());
	}

	/**
	 * action_view()
	 */
	public function action_view($id = null)
	{
		parent::view($id);
	}

	/**
	 * action_create()
	 */
	public function action_create()
	{
		parent::create();
	}

	/**
	 * action_edit()
	 */
	public function action_edit($id = null)
	{
		parent::edit($id);
	}

	/**
	 * action_delete()
	 */
	public function action_delete($id = null)
	{
		parent::delete($id);
	}

	/**
	 * action_undelete()
	 */
	public function action_undelete($id = null)
	{
		parent::undelete($id);
	}

	/**
	 * action_purge_confirm()
	 */
	public function action_purge_confirm($id = null)
	{
		parent::purge_confirm($id);
	}

	/**
	 * action_purge()
	 */
	public function action_purge($id = null)
	{
		parent::purge($id);
	}
}
