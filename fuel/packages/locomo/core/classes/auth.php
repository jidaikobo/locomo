<?php
namespace Locomo;
class Auth
{
	/**
	* @var user information
	*/
	protected static $userinfo = array();
	protected static $is_user_logged_in = false;

	/**
	 * auth()
	 */
	public static function auth($current_action = null, $userinfo = null)
	{
		if($current_action === null) return false;
		$userinfo = $userinfo ?: \Auth::get_userinfo();

		//管理者は許可
		if(
			in_array(-2, $userinfo['usergroup_ids']) ||
			in_array(-1, $userinfo['usergroup_ids'])
		)
		return true;

		//userinfoを確認
		return (in_array($current_action, @$userinfo['acls']));
	}

	/**
	 * owner_auth()
	 */
	public static function owner_auth($controller = null, $action = null, $obj = null, $userinfo = null)
	{
		if( ! $obj) return false;
		$userinfo = $userinfo ?: \Auth::get_userinfo();

		//管理者は許可
		if(
			in_array(-2, $userinfo['usergroup_ids']) ||
			in_array(-1, $userinfo['usergroup_ids'])
		)
		return true;

		//ゲストなど、objを確認できない状態ではowner_authは常にfalse
		if(! method_exists($obj, 'get_default_field_name')) return false;

		//$objのcreator_idカラムを確認し、user_idと比較する。
		$column = isset($obj::$_creator_field_name) ?
			$obj::$_creator_field_name :
			$obj::get_default_field_name('creator');

		if(self::is_exists_owner_auth($controller, $action)):
			return ($obj->{$column} == $userinfo['user_id']);
		else:
			return false;
		endif;
	}

	/**
	 * is_exists_owner_auth()
	 */
	public static function is_exists_owner_auth($controller = null, $action = null)
	{
		$opt = array(
			'where' => array(
				array('controller', $controller),
				array('action', $action),
				array('owner_auth', '!=', null),
			)
		);
		return ! is_null(\Acl\Model_Acl::find('first', $opt));
	}

	/**
	 * set_userinfo()
	 * \Locomo\Controller_Base::before()から呼ばれる。
	 */
	public static function set_userinfo()
	{
		//set userinfo
		$session = \Session::instance();
		static::$userinfo = $session->get('user');
		static::$is_user_logged_in = static::$userinfo ?: false;

		//guest
		if( ! static::$userinfo['user_id']):
			static::$userinfo['user_id'] = 0;
		endif;
		static::$userinfo['usergroup_ids'][] = 0;

		//acl
		$acls = \Config::get('always_allowed');

		//管理者はACLは原則全許可なので確認しない
		if(static::$userinfo['user_id'] >= 0):
			$acl_tmp = \Acl\Model_Acl::find('all',
				array(
					'where' => array(array('usergroup_id', 'IN' , static::$userinfo['usergroup_ids']))
				)
			);
	
			foreach($acl_tmp as $v):
				$acls[] = $v->controller .'/'.$v->action;
			endforeach;
		endif;

		static::$userinfo['acls'] = array_unique($acls);
	}

	/**
	 * is_user_logged_in()
	 */
	public static function is_user_logged_in()
	{
		return (static::$is_user_logged_in) ? true : false;
	}

	/**
	 * is_guest()
	 */
	public static function is_guest()
	{
		return ! static::is_user_logged_in();
	}

	/**
	 * is_user()
	 */
	public static function is_user()
	{
		return static::is_user_logged_in();
	}

	/**
	 * is_admin()
	 */
	public static function is_admin()
	{
		return (static::get_user_id() == -1 || static::get_user_id() == -2);
	}

	/**
	 * is_root()
	 */
	public static function is_root()
	{
		return (static::get_user_id() == -2);
	}

	/**
	 * get_userinfo()
	 */
	public static function get_userinfo($key = null)
	{
		if( ! $key):
			return static::$userinfo;
		endif;

		if($key && isset(static::$userinfo[$key])):
			return static::$userinfo[$key];
		else:
			return false;
		endif;
	}

	/**
	 * get_user() - alias
	 */
	public static function get_user($key = null)
	{
		return static::get_userinfo($key);
	}

	/**
	 * get_user_id()
	 */
	public static function get_user_id()
	{
		return @static::$userinfo['user_id'] ?: false;
	}

	/**
	 * get_usergroups()
	 */
	public static function get_usergroups()
	{
		return @static::$userinfo['usergroup_ids'] ?: array();
	}

	/**
	 * login()
	 */
	public static function login($account, $password)
	{
		$user = false;

		//Banされたユーザだったら追い返す
		$user_model = \User\Model_User::forge();
		if( ! static::check_deny(\Input::post("account"))):
			$user_ban_setting = \Config::get('user_ban_setting');
			$limit_count = $user_ban_setting ? $user_ban_setting['limit_count'] : 3 ;
			$limit_time  = $user_ban_setting ? $user_ban_setting['limit_time'] : 300 ;
			\Session::set_flash('error', "{$limit_count}回のログインに失敗したので、{$limit_time}秒間ログインはできません。");
			\Response::redirect('/');
		endif;

		if($account == null || $password == null):
			\Session::set_flash( 'error', 'ユーザ名とパスワードの両方を入力してください');
			\Response::redirect('user/login/');
		endif;
		//flag and value
		$user = array();

		//rootユーザ
		if($account == ROOT_USER_NAME && $password == ROOT_USER_PASS):
			$user['user_id']       = -2;
			$user['user_name']     = 'root';
			$user['display_name']  = 'root権限管理者';
			$user['usergroup_ids'] = array(-2);
		endif;

		//adminユーザ
		if($account == ADMN_USER_NAME && $password == ADMN_USER_PASS):
			$user['user_id']       = -1;
			$user['user_name']     = 'admin';
			$user['display_name']  = '管理者';
			$user['usergroup_ids'] = array(-1);
		endif;

		//データベースで確認
		if( ! $user || ! @is_numeric($user['user_id'])):
			$user_obj = \User\Model_User::find('first', array(
				'where' => array(
					array('password', '=', static::hash($password)),
					array('created_at', '<=', date('Y-m-d H:i:s')),
					array(
						array('expired_at', '>=', date('Y-m-d H:i:s')),
						'or' => array('expired_at', 'is', null),
					),
					array(
						array('user_name', '=', $account),
						'or' => array('email', '=', $account),
					)),
				)
			);
		if ($user_obj) {
			$user['user_id']       = $user_obj->id;
			$user['user_name']     = $user_obj->user_name;
			$user['display_name']  = $user_obj->display_name;
			$user['usergroup_ids'] = array_keys($user_obj->usergroup);
		}
		endif;

		if($user):
			//session
			$session = \Session::instance();
			$session->set('user', $user);
			static::add_user_log($account, $password, true);
	
			//update last login
			if(isset($user_obj)):
				$user_obj->last_login_at = date('Y-m-d H:i:s');
				$user_obj->save();
			endif;
		endif;

		return $user;
	}

	/**
	 * logout()
	 */
	public static function logout()
	{
		//session
		$session = \Session::instance();
		$session->delete('user');
		\Session::set_flash('success', 'ログアウトしました');
		\Response::redirect('user/login/?ret=/');
	}

	/**
	 * hash()
	 */
	public static function hash($str)
	{
		return md5($str);
	}

	/**
	 * check_deny()
	 * 
	 * @param type $account
	 * @return boolean
	 * by shimizu@hinodeya at bems
	 */
	public static function check_deny($account = null)
	{
		if($account == null) return false;
		$user_ban_setting = \Config::get('user_ban_setting');
		$limit_deny_time  = $user_ban_setting ? $user_ban_setting['limit_deny_time'] : 10 ;
		$limit_count      = $user_ban_setting ? $user_ban_setting['limit_count'] : 3 ;

		$list = \DB::select()->from("loginlog")
						->where("login_id", $account)
						->where("ipaddress", $_SERVER["REMOTE_ADDR"])
						->where("add_at", ">=", \DB::expr("NOW() - INTERVAL " . $limit_deny_time . " MINUTE"))
						->where("count", ">=", $limit_count)
						->execute()->as_array();

		return (count($list) ? false : true);
	}

	/**
	 * add_user_log()
	 * ログを追加
	 * @param type $account
	 * @param type $password
	 * @param type $status
	 * @return boolean
	 * by shimizu@hinodeya at bems
	 */
	public static function add_user_log($account = null, $password = null, $status = false)
	{
		if($account == null || $password == null) return false;
		$password = self::hash($password);

		//設定値
		$user_ban_setting = \Config::get('user_ban_setting');
		$limit_time  = 10 ;
		$limit_count = $user_ban_setting ? $user_ban_setting['limit_count'] : 3 ;

		// 既にデータがあるかどうか
		$list = \DB::select()->from("loginlog")
						//->where("login_id", $account)
						->where("status", 0)
						->where("ipaddress", $_SERVER["REMOTE_ADDR"])
						->where("add_at", ">=", \DB::expr("NOW() - INTERVAL ".$limit_time." SECOND"))
						->limit(1)
						->order_by("add_at", "DESC")
						->execute()->as_array();

		// データがあればカウントアップ
		if (count($list) && ! $status) {
			\DB::update("loginlog")->value("count", $list[0]['count'] + 1)
					->where("loginlog_id", $list[0]['loginlog_id'])
					->execute();

			// 回数が一定以上あればfalseを返却
			if ($limit_count <= $list[0]['count'] + 1) {
				return false;
			} else {
				return true;
			}
		} else {
			// 成功時データを追加
			\DB::insert("loginlog")
					->set(array(
						"login_id"   => $account,
						"login_pass" => $password,
						"status"     => $status,
						"ipaddress"  => $_SERVER['REMOTE_ADDR'],
						"add_at"     => \DB::expr("NOW()"),
						"count"      => 1
					))->execute();

			return true;
		}
		return;
	}
}
