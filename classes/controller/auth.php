<?php
namespace Locomo;
class Controller_Auth extends \Locomo\Controller_Base
{
	/**
	 * action_login()
	 */
	public function action_login()
	{
		// return to
		$dashboard = '/sys/dashboard/';
		$ret = \Input::param('ret', \Input::referrer(), null);
		$ret = $ret == \Uri::create('auth/login/') ? $dashboard : $ret ;
		$ret = $ret == null ? $dashboard : $ret ;
		$ret = substr($ret, 0, 4) == 'http' && substr($ret, 0, strlen(\Uri::base())) != \Uri::base() ? $dashboard : $ret;

		// this action is for guest not logged in users
		if (\Auth::check())
		{
// Princess wants to delete this message :-(
//			\Session::set_flash('error', 'あなたは既にログインしています');
			return \Response::redirect($ret);
		}

		// login check
		if (\Input::method() == 'POST')
		{
			$username = \Input::post('username');
			$password = \Input::post('password');

			// success
			if (\Auth::instance()->login($username, $password))
			{
				\Session::set_flash('success', 'ログインしました。');
				return \Response::redirect($ret);
			}
			// failed
			else
			{
				\Auth::instance()->add_user_log($username, $password, false);
				\Session::set_flash('error', 'ログインに失敗しました。入力内容に誤りがあります。');
				return \Response::redirect('auth/login/');
			}
		}

		// view
		$view = \View::forge('auth/login');
		$view->set('ret', $ret);
		$view->set_global('title', 'ログイン');
		$this->template->content = $view;
	}

	/**
	 * action_logout()
	 */
	public function action_logout()
	{
		// remove the remember-me cookie, we logged-out on purpose
		\Auth::dont_remember_me();
		
		// logout
		\Auth::logout();
		\Session::set_flash('success', 'ログアウトしました。');
		return \Response::redirect('auth/login/?ret=/');
	}
}
