<?php
namespace User;
class Controller_Auth extends \Locomo\Controller_Crud
{
	/**
	 * action_login()
	 */
	public function action_login()
	{
		$ret = \Input::param('ret', @$_SERVER['HTTP_REFERER'], null);
		$ret = (in_array($ret, array(\Uri::create('user/auth/login/'), \Uri::create('user/auth/login/')))) ? '/' : $ret ;
		$ret = $ret == null ? '/admin/dashboard/' : $ret ;

		//ログイン済みのユーザだったらログイン画面を表示しない
		if(\Auth::check()):
			\Session::set_flash('error', 'あなたは既にログインしています');
			\Response::redirect_back();
		endif;

		//ログイン処理
		if(\Input::method() == 'POST'):
			$username = \Input::post('username');
			$password = \Input::post('password');

			//ログイン成功
			if(\Auth::instance()->login($username, $password)):
				\Session::set_flash('success', 'ログインしました。');
				return \Response::redirect($ret);
			else:
				//ログイン失敗
				\Auth::instance()->add_user_log($username, $password, false);
				\Session::set_flash('error', 'ログインに失敗しました。入力内容に誤りがあります。');
				return \Response::redirect('user/auth/login/');
			endif;
		endif;

		//view
		$view = \View::forge('login');
		$view->set('ret', $ret);
		$view->set_global('title', 'ログイン');
		$view->base_assign();
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
		\Response::redirect_back();
	}
}
