<?php
namespace Kontiki;
abstract class Controller_User_Abstract extends \Kontiki\Controller_Crud
{
	/**
	* @var user information
	*/
	public static $userinfo = array();

	/**
	* @var bool is_user_logged_in
	*/
	public static $is_user_logged_in = false;

	/**
	 * test datas
	 */
	protected $test_datas = array(
		'user_name'   => 'text',
		'password'    => 'text:test',
		'email'       => 'email',
		'status'      => 'text:public',
		'creator_id'  => 'int',
		'modifier_id' => 'int',
	);

	/**
	 * post_save_hook()
	 */
	public function post_save_hook($obj = NULL, $mode = 'edit')
	{
		//ユーザが所属するグループを更新
		if (\Input::method() == 'POST'):
			$user_id = intval($obj->id);
			//まずすべて削除
			$q = \DB::delete();
			$q->table('users_usergroups_r');
			$q->where('user_id', $user_id);
			$q->execute();

			//ユーザグループを更新
			if(is_array(\Input::post('usergroup'))):
				foreach(\Input::post('usergroup') as $group_id => $v):
					$group_id = intval($group_id);

					$q = \DB::insert();
					$q->table('users_usergroups_r');
					$q->set(array(
						'user_id' => $user_id,
						'usergroup_id' => $group_id,
					));
					$q->execute();

				endforeach;
			endif;
		endif;
		return $obj;
	}

	/**
	 * owner_acl()
	 * creator_idだけでなく、ユーザIDが一致したら許可する
	*/
	public function owner_acl($userinfo = null, $current_action = null, $item = null)
	{
		if($userinfo == null || $current_action == null || $item == null) return false;
		$result = parent::owner_acl($userinfo, $current_action, $item);

		//parentでやってるけど、こちらでもアクションの存在確認は必要。なければfalse
		if( ! \Acl\Controller_Acl::owner_auth($current_action, $userinfo)) return false;

		//creator_idか、個票のidが一致したら、true
		$is_users_item = ($userinfo['user_id'] === $item->id);
		return ($result || $is_users_item);
	}

	/**
	 * set_userinfo()
	 * ログイン中のユーザ情報のセット。
	 * \Kontiki\Controller::before()から呼ばれる。
	 */
	public static function set_userinfo()
	{
		//set userinfo
		$session = \Session::instance();
		self::$userinfo = $session->get('user');

		//is_user_logged_in
		self::$is_user_logged_in = (self::$userinfo) ? true : false;

		//guest
		if( ! self::$userinfo['user_id']):
			self::$userinfo['user_id'] = null;
		endif;
		self::$userinfo['usergroup_ids'][] = 0;

		//acl
		$q = \DB::select('controller','action');
		$q->from('Acls');
		$q->where('usergroup_id', 'in', self::$userinfo['usergroup_ids']);
		$q->or_where('user_id', '=', self::$userinfo['user_id']);
		$acls = array('content/home');
		foreach($q->execute()->as_array() as $v):
			$acls[] = $v['controller'].'/'.$v['action'];
		endforeach;
		self::$userinfo['acls'] = array_unique($acls);

		//owner acl
		//ゲストやadminは確認する必要がない
		self::$userinfo['acls_ower'] = array();
		if(self::$userinfo['user_id'] >= 1):
			$q = \DB::select('controller','action');
			$q->from('Acls');
			$q->where('owner_auth', 1);
			$acls_ower = array();
			foreach($q->execute()->as_array() as $v):
				$acls_ower[] = $v['controller'].'/'.$v['action'];
			endforeach;
			self::$userinfo['acls_ower'] = array_unique($acls_ower);
		endif;

		//view
		$view = \View::forge();
		$view->set_global('user', self::$userinfo);
		$view->set_global('is_user_logged_in', (self::$is_user_logged_in) ? true : false);
	}

	/**
	 * action_login()
	 */
	public function action_login($redirect = NULL)
	{
		//redirect
		$redirect_decode = $redirect ? base64_decode($redirect) : \URI::base() ;

		//ログイン済みのユーザだったらログイン画面を表示しない
		if(self::$is_user_logged_in):
			\Session::set_flash( 'error', 'あなたは既にログインしています');
			\Response::redirect($redirect_decode);
		endif;

		//ログイン処理
		if(\Input::method() == 'POST'):
			//Banされたユーザだったら追い返す
			$user_model = \User\Model_User::forge();
			if( ! $user_model::check_deny(\Input::post("account"))):
				$user_ban_setting = \Config::get('user_ban_setting');
				$limit_count = $user_ban_setting ? $user_ban_setting['limit_count'] : 3 ;
				$limit_time  = $user_ban_setting ? $user_ban_setting['limit_time'] : 300 ;
				\Session::set_flash( 'error', "{$limit_count}回のログインに失敗したので、{$limit_time}秒間ログインはできません。");
				\Response::redirect('/');
			endif;

			$account = \Input::post('account');
			$password = \Input::post('password');
			if($account == null || $password == null):
				\Session::set_flash( 'error', 'ユーザ名とパスワードの両方を入力してください');
				\Response::redirect('user/login/');
			endif;
			//flag and value
			$user = array();
			$is_success = false;

			//rootユーザ
			if($account == ROOT_USER_NAME && $password == ROOT_USER_PASS):
				$user['user_id'] = -2;
				$user['usergroup_ids'] = array(-2);
				$is_success = true;
			endif;

			//adminユーザ
			if($account == ADMN_USER_NAME && $password == ADMN_USER_PASS):
				$user['user_id'] = -1;
				$user['usergroup_ids'] = array(-1);
				$is_success = true;
			endif;

			//データベースで確認
			$user_id = 0;
			if( ! $user || ! @is_numeric($user['user_id'])):
				$user_ids = $user_model::get_userinfo($account, $password);
				$user_id = @$user_ids['id'] ;
			endif;

			//ユーザが存在したらUsergroupを取得
			if($user_id):
				$usergroup_ids = $user_model::get_usergroups($user_id);
				//DBに存在したユーザ情報
				$user['user_id'] = $user_id;
				$user['usergroup_ids'] = $usergroup_ids ? $usergroup_ids : array();
				$is_success = true;
			endif;

			//ログイン成功
			if($is_success):
				//session
				$session = \Session::instance();
				$session->set('user', $user);
				$user_model::add_user_log($account, $password, true);

				//redirect
				\Session::set_flash( 'success', 'ログインしました。');
				\Response::redirect($redirect_decode);
			//ログイン失敗
			else:
				$user_model::add_user_log($account, $password, false);
				\Session::set_flash( 'error', 'ログインに失敗しました。入力内容に誤りがあります。');
				\Response::redirect('user/login/');
			endif;
		endif;

		//view
		$view = \View::forge('login');
		$view->set('ret', $redirect);
		$view->set_global('title', 'ログイン');
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_logout()
	 */
	public function action_logout()
	{
		//session
		$session = \Session::instance();
		$session->delete('user');
		\Session::set_flash( 'success', 'ログアウトしました');
		\Response::redirect('user/login/');
	}
}
