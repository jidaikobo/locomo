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
	 * action_index_admin()
	 */
	public function action_index_admin($pagenum = 1)
	{
		\Model_Usrgrp_Custom::$_options = array(
			'where' => array(
				array('is_available', true),
				array('is_for_acl', false),
				array('customgroup_uid', \Auth::get('id')),
			),
			'order_by' => array('seq' => 'ASC', 'name' => 'ASC'),
		);
		parent::index_admin();
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
}
