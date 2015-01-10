<?php
namespace Locomo;
class Controller_Flr extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Testdata;
	use \Controller_Traits_Crud;
	use \Controller_Traits_Revision;
	use \Controller_Traits_Bulk;

	// locomo
	public static $locomo = array(
		'nicename'     => 'ファイル', // for human's name
		'explanation'  => 'ファイルの閲覧やアップロードを行います。', // for human's explanation
		'main_action'  => 'index_admin', // main action
		'main_action_name' => 'ファイル管理', // main action's name
		'main_action_explanation' => 'アップロードされたファイルの閲覧を行います。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => true, // true: hide from admin bar
		'order'        => 1030, // order of appearance
		'widgets' =>array(
		),
	);

	/**
	 * action_index()
	 * user module is not for public.
	 */
	public function action_index()
	{
		return \Response::redirect(\Uri::create('usr/index_admin'));
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		if (\Input::get('from')) \Model_Usr::$_conditions['where'][] = array('created_at', '>=', \Input::get('from'));
		if (\Input::get('to'))   \Model_Usr::$_conditions['where'][] = array('created_at', '<=', \Input::get('to'));
		parent::index_admin();
	}

	/**
	 * user_auth_find()
	 */
	public static function user_auth_find()
	{
		// honesty at this case, ($pkid == \Auth::get('id')) is make sence.
		// this is a sort of sample code.
		$pkid = \Request::main()->id;
		$obj = \Model_Usr::find($pkid);

		// add allowed to show links at actionset
		\Auth::instance()->add_allowed(array(
			'\\Controller_Usr/edit',
			'\\Controller_Usr/view',
		));

		return ($obj->id == \Auth::get('id'));
	}
}
