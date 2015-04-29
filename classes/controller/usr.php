<?php
namespace Locomo;
class Controller_Usr extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Testdata;
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

	// model_name
	protected $model_name = '\Locomo\Model_Usr';

	/**
	 * before()
	 */
	public function before()
	{
		parent::before();
		// locomo_has_access
		\Event::register('locomo_has_access', '\Controller_Usr::user_auth_find');
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		parent::index_admin();
	}

	/**
	 * action_index_yet()
	 */
	public function action_index_yet()
	{
		parent::index_yet();
	}

	/**
	 * action_index_expired()
	 */
	public function action_index_expired()
	{
		parent::index_expired();
	}

	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible()
	{
		parent::index_invisible();
	}

	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted()
	{
		parent::index_deleted();
	}

	/*
	 * action_index_all()
	 */
	public function action_index_all()
	{
		parent::index_all();
	}

	/**
	 * action_view()
	 */
	public function action_view($id = null)
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
	public function action_edit($id = null)
	{
		parent::edit($id);
	}

	/**
	 * action_delete()
	 */
	public function action_delete($id = null)
	{
		parent::delete($id);
	}

	/**
	 * action_undelete()
	 */
	public function action_undelete($id = null)
	{
		parent::undelete($id);
	}

	/**
	 * action_purge_confirm()
	 */
	public function action_purge_confirm($id = null)
	{
		parent::purge_confirm($id);
	}

	/**
	 * action_purge()
	 */
	public function action_purge($id = null)
	{
		parent::purge($id);
	}

	/*
	 * ajax グループIDからユーザリストを返す
	 * shimizu@hinodeya-ecolife.com
	 * @return users の配列
	 */
	public function post_user_list()
	{
		if (!\Input::is_ajax()) throw new \HttpNotFoundException;;
		$where = array();

		$gid = \Input::post("gid", 0);

		switch ($gid)
		{
			// guest users - return nothing
			case 0:
				$where = array(array('usergroup.id', '=', 0));
				break;
			// all logged in users
			case -10:
				$where = array();
				break;
			// return users in group
			default:
				$where = array(array('usergroup.id', '=', $gid));
				break;
		}

		$response = \Model_Usr::find('all',
			array(
				'related' => count($where) ? array('usergroup') : array(),
				'where'=> $where,
				'order_by' => array('username' => 'asc')
				)
			);
		$result = array();
		$index = 0;
		foreach ($response as $row) {
//			$row[0] = $index;
			$index++;
			$result[] = $row;
		}
		echo $this->response($result, 200); die();
	}

	/**
	 * user_auth_find()
	 * Event at locomo_has_access of \Auth\Auth_Acl_Locomoacl::has_access()
	 */
	public static function user_auth_find($condition)
	{
		$checks = array(
			'\Controller_Usr/view',
			'\Controller_Usr/edit',
			'\Controller_Usr/reset_paswd'
		);
		if ( ! in_array($condition, $checks) || ! \Request::main()->controller == 'Controller_Usr')
		{
			return 'through';
		}
		$checks = array('view', 'edit', 'reset_paswd');
		if ( ! in_array(self::$action, $checks))
		{
			return 'through';
		}

		// honesty at this case, ($pkid == \Auth::get('id')) is make sence.
		// this is a sort of sample code.
		$pkid = \Request::main()->id;
		$obj = \Model_Usr::find($pkid);
		if ( ! $obj) return false;

		// add allowed to show links at actionset
		\Auth::instance()->add_allowed(array(
			'\Controller_Usr/edit',
			'\Controller_Usr/view',
		));

		return ($obj->id == \Auth::get('id')) ;
	}

	/**
	 * action_reset_paswd()
	 */
	public function action_reset_paswd($id)
	{
		// vals
		$model = $this->model_name ;
		$content = \View::forge('defaults/edit');

		if ($id)
		{
			$obj = $model::find($id, $model::authorized_option(array(), 'edit'));
			// not found
			if ( ! $obj)
			{
				\Session::set_flash('error', '存在しないユーザです。');
				return \Response::redirect(\Uri::create('usr/index_admin'));
			}
		}
		$form = $model::reset_paswd_form('edit', $obj);

$is_sendmail = true;

		// save
		if (\Input::post())
		{
			if ( ! \Security::check_token())
			{
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			} else {
				if (static::reset_paswd($obj, $is_sendmail))
				{
					//success
					\Session::set_flash('success', 'パスワードをリセットして、メールを送信しました。');
				} else {
					\Session::set_flash('error', 'パスワードリセットを失敗しました。再度試してください。');
				}
			}
		}

		//view
		$this->template->set_global('title', 'パスワードリセット');
		$content->set_global('item', $obj, false);
		$content->set_global('form', $form, false);
		$this->template->content = $content;
		static::set_object($obj);
	}

	/**
	 * action_bulk_reset_paswd()
	 */
	public function action_bulk_reset_paswd()
	{
		// vals
		$model = $this->model_name ;
		$content = \View::forge('defaults/edit');

		if ( ! \Auth::is_root())
		{
			\Session::set_flash('error', '権限がありません。');
			return \Response::redirect(\Uri::create('usr/index_admin'));
		}

		$objs = $model::find('all');

		$form = $model::reset_paswd_form('bulk', $objs);

		// save
		if (\Input::post())
		{
			if ( ! \Security::check_token())
			{
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			} else {
				foreach ($objs as $obj)
				{
					static::reset_paswd($obj, $is_sendmail = true);
				}
				\Session::set_flash('success', '一括でパスワードをリセットして、メールを送信しました。');

				// set up email
				$body = var_export(static::$generated, 1);
				$email = \Email::forge();
				$email->from('webmaster@kyoto-lighthouse.org', 'ライトスタッフシステム');
				$email->to('shibata@jidaikobo.com', $obj->display_name);
				$email->subject('すべてのパスワードのお知らせです');
				$email->body($body);

				// send
				$email->send();
			}
		}

		//view
		$this->template->set_global('title', 'パスワードリセット');
		$content->set_global('form', $form, false);
		$this->template->content = $content;
	}

	/**
	 * reset_paswd()
	*/
	protected static $generated = array();
	public static function reset_paswd($obj, $is_sendmail = false)
	{
		if ( ! is_object($obj)) return false;
	
		// package and config
		\Package::load('email');
		$site_title = \Config::get('site_title');

		// disable_event
		$obj->disable_event('before_save');

		// save password
		$pswd = substr(md5(microtime()), 0, 8);
		$obj->password = \Auth::hash_password($pswd);

		if ($obj->save())
		{
			// mail text
			$body = '';
			$body.= $obj->display_name."さま\n\n";
			$body.= "パスワードリセットを行いました。\n";
			$body.= "下記情報に沿ってログインしてください。\n\n";
			$body.= \Uri::base()."\n";
			$body.= 'username: '.$obj->email."\n";
			$body.= 'password: '.$pswd."\n\n";
			$body.= "-- \n";
			$body.= $site_title."\n";
			$body.= date('Y-m-d H:i:s')."\n";
	
			// set up email
			$email = \Email::forge();
			$email->from('webmaster@kyoto-lighthouse.org', 'ライトスタッフシステム');
			$email->to($obj->email, $obj->display_name);
			$email->subject('【'.$site_title.'】パスワードのお知らせ');
			$email->body($body);

			static::$generated[] = array($obj->display_name, $obj->email, $pswd);

			// send
			try
			{
				$email->send();
			}
			catch(\EmailValidationFailedException $e)
			{
				// バリデーションが失敗したとき
			}
			catch(\EmailSendingFailedException $e)
			{
				// ドライバがメールを送信できなかったとき
			}
		}
		return true;
	}
}
