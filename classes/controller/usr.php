<?php
namespace Locomo;
class Controller_Usr extends \Locomo\Controller_Base
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 10,
		'is_for_admin' => true,
		'admin_home' => '\\Controller_Usr/index_admin',
		'admin_home_name' => '管理一覧',
		'nicename' => 'ユーザ',
		'actionset_classes' =>array(
			'base'   => '\\Actionset_Base_Usr',
			'index'  => '\\Actionset_Index_Usr',
			'option' => '\\Actionset_Option_Usr',
		),
		'widgets' =>array(
			array('name' => '新規ユーザ一覧', 'uri' => '\\Controller_Usr/index_admin?order_by%5B0%5D%5B0%5D=id&order_by%5B0%5D%5B1%5D=desc'),
			array('name' => '新規ユーザ登録', 'uri' => '\\Controller_Usr/create'),
		),
	);

	//trait
	use \Controller_Traits_Testdata;
	use \Controller_Traits_Crud;
	use \Controller_Traits_Revision;
	use \Controller_Traits_Bulk;

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
