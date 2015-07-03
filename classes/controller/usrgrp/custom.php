<?php
namespace Locomo;
class Controller_Usrgrp_Custom extends \Locomo\Controller_Base
{
	// traits
//	use \Controller_Traits_Revision;
//	use \Controller_Traits_Bulk;

	// locomo
	public static $locomo = array(
		'nicename'     => 'カスタムユーザグループ', // for human's name
		'explanation'  => 'カスタムユーザグループの編集をします。',
		'main_action'  => 'index_admin', // main action
		'main_action_name' => 'カスタムユーザグループ管理', // main action's name
		'main_action_explanation' => 'カスタムユーザグループの編集をします。', // explanation of top page
		'show_at_menu' => false, // true: show at admin bar and admin/home
		'is_for_admin' => false, // true: hide from admin bar
		'order'        => 1020, // order of appearance
	);

	/**
	 * before()
	 */
	public function before()
	{
		// parent
		parent::before();

		// is_use_customusergroup
		$is_use_customusergroup = \Config::get('is_use_customusergroup');
		if ($is_use_customusergroup)
		{
			// index_admin
			$actions = array(
				'\Controller_Usrgrp_Custom/index_admin',
				'\Controller_Usrgrp_Custom/index_unavailable',
				'\Controller_Usrgrp_Custom/create',
				'\Controller_Usrgrp_Custom/edit',
				'\Controller_Usrgrp_Custom/view',
				'\Controller_Usrgrp_Custom/delete',
				'\Controller_Usrgrp_Custom/purge_confirm',
				'\Controller_Usrgrp_Custom/purge',
			);
			\Auth::instance()->add_allowed($actions);

			// check item's creator_id
			$pkid = \Request::main()->id;
			$obj = \Model_Usrgrp_Custom::find($pkid);

			if ($obj)
			{
				// actions
				$actions = array(
					'\Controller_Usrgrp_Custom/edit',
					'\Controller_Usrgrp_Custom/view',
					'\Controller_Usrgrp_Custom/purge_confirm',
					'\Controller_Usrgrp_Custom/purge',
				);
		
				// modify \Auth::get('allowed')
				if ($obj->customgroup_uid !== \Auth::get('id'))
				{
					\Auth::instance()->remove_allowed($actions);
				}
			}
		}
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin($pagenum = 1)
	{
		parent::index_admin($pagenum);
	}

	/**
	 * action_index_unavailable()
	 */
	public function action_index_unavailable($pagenum = 1)
	{
		parent::index_unavailable($pagenum);
	}

	/**
	 * action_view()
	 */
	public function action_view($id)
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
	public function action_edit($id)
	{
		parent::edit($id);
	}

	/**
	 * action_purge_confirm()
	 */
	public function action_purge_confirm($id)
	{
		parent::purge_confirm($id);
	}

	/**
	 * action_purge()
	 */
	public function action_purge()
	{
		parent::purge();
	}
}
