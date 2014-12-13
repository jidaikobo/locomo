<?php
namespace User;
class Controller_User extends \Locomo\Controller_Crud
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 10,
		'is_for_admin' => true,
		'admin_home' => '\\User\\Controller_User/index_admin',
		'admin_home_name' => '管理一覧',
		'nicename' => 'ユーザ',
		'help'     => 'packages/locomo/modules/user/help/user.html',//app/../からのパス
		'actionset_classes' =>array(
			'base'   => '\\User\\Actionset_Base_User',
			'index'  => '\\User\\Actionset_Index_User',
			'option' => '\\User\\Actionset_Option_User',
		),
		'widgets' =>array(
			array('name' => '新規ユーザ一覧', 'uri' => '\\User\\Controller_User/index_admin?orders%5Bid%5D=desc'),
			array('name' => '新規ユーザ登録', 'uri' => '\\User\\Controller_User/create'),
		),
	);

	//trait
	use \Locomo\Controller_Traits_Testdata;
	use \Revision\Traits_Controller_Revision;
	use \Bulk\Traits_Controller_Bulk;

	/**
	 * action_index()
	 * user module is not for public.
	 */
	public function action_index()
	{
		return \Response::redirect(\Uri::create('user/user/index_admin'));
	}

	/**
	 * user_auth_find()
	 */
	public static function user_auth_find()
	{
		// honesty at this case, ($pkid == \Auth::get('id')) is make sence.
		// this is a sort of sample code.
		$pkid = \Request::main()->id;
		$obj = \User\Model_User::find($pkid);

		// add allowed to show links at actionset
		\Auth::instance()->add_allowed(array(
			'\\User\\Controller_User/edit',
			'\\User\\Controller_User/view',
		));

		return ($obj->id == \Auth::get('id'));
	}
}
