<?php
namespace Kontiki;
class Rig
{
	public static function _init()
	{
		$session = \Session::instance();
//		\User\Controller_User::$userinfo = $session->get('user');
		$userinfo = $session->get('user');
		$view = \View::forge();
//		$view->set_global('user', self::$userinfo);
		$view->set_global('user', $userinfo);
		$view->set_global('is_user_logged_in', ($userinfo) ? true : false);
	}
}
