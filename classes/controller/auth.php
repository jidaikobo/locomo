<?php
namespace Locomo;
class Controller_Auth extends \Locomo\Controller_Base
{
	/**
	 * before()
	 */
	public function before()
	{
		// アクティベーションしないまま、設定期日を超えたユーザはdeleteする
		$days = intval(\Config::get('user_registration_limit_days'));
		if ($days)
		{
			$target = time() - ($days * 86400);
			$users = \Model_Usr::find('all', array(
				'where' => array(
					array('created_at', '=<', $target),
					array('is_visible', false)
				)
			));
			foreach ($users as $user)
			{
				$user->delete(null, true);
			}
		}
	}

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

	/**
	 * action_registration()
	*/
	public function action_registration()
	{
		// prohibit
		$type = \Config::get('user_registration_type');
		$errors = array();
		if (\Auth::check()) $errors[] = 'すでにログインしています。' ;
		if ($type == 'by_admin') $errors[] = 'このサイトはユーザ登録を受け付けていません。' ;
		if ($errors)
		{
			\Session::set_flash('error', $errors);
			return \Response::redirect(\Uri::create('/'));
		}

		// re-registration
		if (\Input::get('email') && \Input::get('activation_key'))
		{
			// 再度登録するユーザはdeltedの筈なので、いったんpurge()する
			$cond = array(
				'where' => array(
					'activation_key' => $key,
					'email' => $email,
				),
			);
			$deleted = \Model_Usr::find_deleted('first', $cond);
			$deleted->purge(null, true);
		}

		// vals
		$content = \Presenter::forge('auth/registration');
		$item = \Model_Usr::forge();
		if (\Input::get('email')) $item->email = \Input::get('email');
		$form = $content::form($item);

		// save
		if (\Input::post())
		{
			// ログインしているわけではないので、トークンはチェックしない
			$item->cascade_set(\Input::post(), $form, $repopulate = true);
			$errors = $form->error() ?: array() ;

			// ユーザID、メールアドレスがすでに存在している場合は、エラー
			$cond = array(
				'where' => array(
					'username' => \Input::post('username'),
					'or' => array(
						array('email', \Input::post('email')),
					)
				),
			);
			$exist = \Model_Usr::find('first', $cond);

			// 削除済みユーザにいてもダメ
			$exist = $exist ?: \Model_Usr::find_deleted('first', $cond);
			if ($exist) $errors[] = 'ユーザ名かメールアドレスがすでに使われています。';

			// default values
			$item->is_visible = 0;
			$default_usergroup_id = \Config::get('default_usergroup_id');
			if ($default_usergroup_id)
			{
				$ug = Model_Usrgrp::find($default_usergroup_id);
				$item->usergroup[] = $ug;
			}

			// save
			if ( ! $errors && $item->save(null, true))
			{
				// event
				$event = 'locomo_registration_succeed';
				$item = \Event::instance()->has_events($event) ? \Event::instance()->trigger($event, $item) : $item ;
				\Session::set_flash('success', '登録申請をしました。');
				return \Response::redirect(\Uri::create('/auth/pre_registration/'.$item->id));
			}
			else
			{
				// event
				$event = 'locomo_registration_failed';
				if (\Event::instance()->has_events($event)) \Event::instance()->trigger($event);
				$errors[] = '登録申請に失敗しました。';
			}
		}

		// set_flash()
		if ($errors) \Session::set_flash('error', $errors);

		//view
		$this->template->set_global('title', 'ユーザ登録');
		$this->template->set_global('item', $item, false);
		$this->template->set_global('form', $form, false);
		$this->template->set_safe('content', $content);
	}

	/**
	 * action_pre_registration()
	 */
	public function action_pre_registration($uid)
	{
		$type = \Config::get('user_registration_type');

		// find valid user
		$cond = array(
			'where' => array(
				'id' => $uid,
				'is_visible' => false,
			),
		);
		$item = \Model_Usr::find('first', $cond);

		// assign
		$content = \View::forge('auth/pre_registration');
		$this->template->set_global('item', $item, false);

		// mail
		if ($item)
		{
			\Package::load('email');

			if ($type == 'by_user_only')
			{
				$mail_raw = (string) \View::forge('auth/email_by_user_only');
			}
			else
			{
				$mail_raw = (string) \View::forge('auth/email_by_user_admin');
			}

			$mail = Util::parse_email($mail_raw);
			$this->send($mail['headers']['From_email'],
						$mail['headers']['From_str'],
						$item->email,
						$item->display_name,
						$mail['headers']['Subject'],
						$mail['body']);

			if ($type == 'by_user_admin')
			{
				$mail_raw = (string) \View::forge('auth/email_by_user_admin_to_admin');
				$mail = Util::parse_email($mail_raw);
				$this->send($mail['headers']['From_email'],
							$mail['headers']['From_str'],
							$item->email,
							$item->display_name,
							$mail['headers']['Subject'],
							$mail['body']);
			}
		}

		//view
		$this->template->set_global('type', $type);
		$this->template->set_global('title', 'ユーザ登録の開始');
		$this->template->set_safe('content', $content);
	}

	/**
	 * action_activation()
	 */
	public function action_activation($key = null, $email = null)
	{
		is_null($key) || is_null($email) and \Response::redirect(\Uri::create('/'));
		$view = \View::forge('auth/activation');

		$cond = array(
			'where' => array(
				'activation_key' => $key,
				'email' => $email,
				'is_visible' => false,
			),
		);
		$exist = \Model_Usr::find('first', $cond);

		if ($exist)
		{
			$exist->is_visible = true;
			$exist->save();

			$view->set('item', $exist);

			$mail_raw = (string) \View::forge('auth/email_user_activated');
			$mail = Util::parse_email($mail_raw);
			$this->send($mail['headers']['From_email'],
									$mail['headers']['From_str'],
									$item->email,
									$item->display_name,
									$mail['headers']['Subject'],
									$mail['body']);

			$mail_raw = (string) \View::forge('auth/email_to_admin');
			$mail = Util::parse_email($mail_raw);
			$this->send($mail['headers']['From_email'],
									$mail['headers']['From_str'],
									$item->email,
									$item->display_name,
									$mail['headers']['Subject'],
									$mail['body']);
		}

		// 登録済みユーザ
		$cond = array(
			'where' => array(
				'activation_key' => $key,
				'email' => $email,
				'is_visible' => true,
			),
		);
		$activated = \Model_Usr::find('first', $cond);

		// 削除済み（期限切れ）ユーザ
		$cond = array(
			'where' => array(
				'activation_key' => $key,
				'email' => $email,
			),
		);
		$deleted = \Model_Usr::find_deleted('first', $cond);

		// view
		$view->set('activated', $activated);
		$view->set('deleted', $deleted);
		$view->set_global('title', 'ユーザ登録承認');
		$this->template->content = $view;
	}

	/**
	 * send()
	 */
	private function send ($from, $from_str, $to, $to_str, $subject, $body)
	{
		$email = \Email::forge();
		$email->from($from, $from_str);
		$email->to($to, $to_str);
		$email->subject($subject);
		$email->body($body);
		$email->send();
	}
}
