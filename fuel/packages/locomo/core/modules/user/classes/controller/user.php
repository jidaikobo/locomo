<?php
namespace User;
class Controller_User extends \Locomo\Controller_Crud
{
	//trait
	use \Locomo\Controller_Traits_Testdata;
	use \Revision\Traits_Controller_Revision;
	use \Bulk\Traits_Controller_Bulk;

	/**
	 * action_login()
	 */
	public function action_login()
	{
		$ret = \Input::param('ret', @$_SERVER['HTTP_REFERER']);
		$ret = $ret == null ? '/' : $ret ;

		//ログイン済みのユーザだったらログイン画面を表示しない
		if(\Auth::is_user_logged_in()):
			\Session::set_flash('error', 'あなたは既にログインしています');
			\Response::redirect($ret);
		endif;

		//ログイン処理
		if(\Input::method() == 'POST'):
			$account = \Input::post('account');
			$password = \Input::post('password');

			//ログイン成功
			if(\Auth::login($account, $password)):
				\Session::set_flash( 'success', 'ログインしました。');
				\Response::redirect($ret);
			else:
				//ログイン失敗
				\Auth::add_user_log($account, $password, false);
				\Session::set_flash( 'error', 'ログインに失敗しました。入力内容に誤りがあります。');
				\Response::redirect('user/login/');
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
		\Auth::logout();
	}

	/**
	 * action_usergroup()
	 */
	public function action_usergroup()
	{
		$view = \View::forge(PKGCOREPATH.'modules/bulk/views/bulk.php');

		\User\Model_Usergroup::disable_filter();
		//	\Locomo\Bulk::set_define_function('ctm_func');
		
		$form = $this->bulk(array(), array('per_page' => 2), '\User\Model_Usergroup');

		$view->set_global('title', 'ユーザグループ設定');
		$view->set_global('form', $form, false);

		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', \Pagination::get('total_items')); ///
		$view->base_assign();
		$this->template->content = $view;
	}
}
