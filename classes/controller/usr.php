<?php
namespace Locomo;
class Controller_Usr extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Testdata;
	use \Controller_Traits_Crud;
	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'nicename'     => 'ユーザ', // for human's name
		'explanation'  => 'システムを利用するユーザの新規作成、編集、削除等を行います。',
		'main_action'  => 'index_admin', // main action
		'main_action_name' => 'ユーザ管理', // main action's name
		'main_action_explanation' => '既存ユーザの一覧です。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => true, // true: hide from admin bar
		'order'        => 1010, // order of appearance
		'widgets' =>array(
			array('name' => '新規ユーザ一覧', 'uri' => '\\Controller_Usr/index_admin?order_by%5B0%5D%5B0%5D=id&order_by%5B0%5D%5B1%5D=desc'),
			array('name' => '新規ユーザ登録', 'uri' => '\\Controller_Usr/create'),
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
	 * index_core()
	 */
	public function index_core()
	{
		parent::index_core();
		$search_form = \Model_Usr::search_form();
		$this->template->content->set_safe('search_form', $search_form);
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		if (\Input::get('from')) \Model_Usr::$_conditions['where'][] = array('created_at', '>=', \Input::get('from'));
		if (\Input::get('to'))   \Model_Usr::$_conditions['where'][] = array('created_at', '<=', \Input::get('to'));
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
