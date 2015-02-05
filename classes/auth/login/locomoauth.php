<?php
namespace Locomo;
class Auth_Login_Locomoauth extends \Auth\Auth_Login_Driver
{
	/**
	 * properties
	 */

	protected $user;
	private static $_roots;
	private static $_admins;
	private static $_alladmins;
	protected $_root_info = array(
		'id'           => -2,
		'display_name' => 'root管理者',
		'usergroups'   => array(-1,-2),
	);
	protected $_admin_info = array(
		'id'           => -1,
		'display_name' => '管理者',
		'usergroups'   => array(-1),
	);
	protected static $guest_login = array(
		'id' => 0,
		'username' => 'guest',
		'group' => '0',
		'login_hash' => false,
		'email' => false
	);
	protected $config = array(
		'drivers' => array('group' => array('\\Locomo\\Locomogroup')),
		'additional_fields' => array('profile_fields'),
	);

	/**
	 * Load the config and setup the remember-me session if needed
	 */
	public static function _init()
	{
		\Config::load('locomoauth', true, true, true);

		// admins
		$alladmins = unserialize(LOCOMO_ADMINS);
		static::$_roots  = array_keys(\Arr::get($alladmins, 'root', array()));
		static::$_admins = array_keys(\Arr::get($alladmins, 'admin', array()));
		$allnames        = array_unique(array_merge(static::$_roots, static::$_admins));
		if (count(static::$_roots)+count(static::$_admins) <> count($allnames))
		{
			throw new \OutOfBoundsException('administrators\' username is must be unique.');
		}
		static::$_alladmins = $allnames;

		// setup the remember-me session object if needed
		if (\Config::get('locomoauth.remember_me.enabled', false))
		{
			static::$remember_me = \Session::forge(array(
				'driver' => 'cookie',
				'cookie' => array(
					'cookie_name' => \Config::get('locomoauth.remember_me.cookie_name', 'rmcookie'),
				),
				'encrypt_cookie' => true,
				'expire_on_close' => false,
				'expiration_time' => \Config::get('locomoauth.remember_me.expiration', 86400 * 31),
			));
		}
	}

	/**
	 * Check for login
	 *
	 * @return  bool
	 */
	protected function perform_check()
	{
		// fetch the username and login hash from the session
		$username    = \Session::get('username');
		$login_hash  = \Session::get('login_hash');

		// admins
		if (in_array($username, static::$_alladmins))
		{
			$db_login_hash = \DB::select('login_hash')
				->from('lcm_usr_admins')
				->where(array(array('username', '=', $username)))
				->execute()->current();

			if ($db_login_hash['login_hash'] == $login_hash)
			{
				// is_root
				if (in_array($username, static::$_roots))
				{
					\Arr::set($this->_root_info, 'username', $username);
					$this->user = $this->_root_info;
				}
				else
				{
					\Arr::set($this->_admin_info, 'username', $username);
					$this->user = $this->_admin_info;
				}
				return true;
			}
		}

		// always_allowed
		$acls = \Config::get('always_allowed');

		// only worth checking if there's both a username and login-hash
		if ( ! empty($username) and ! empty($login_hash))
		{
			if (is_null($this->user) or ($this->user['username'] != $username and $this->user != static::$guest_login))
			{
				// occasionally, Fuel lost Model_Usr by unidentified reason...
				if (class_exists('Model_Usr'))
				{
					$this->user = \Model_Usr::find('first', array('where' => array(array('username', $username))));
				}
				else
				{
					$this->user = null;
				}
			}
			// return true when login was verified, and either the hash matches or multiple logins are allowed
			if ($this->user and (\Config::get('locomoauth.multiple_logins', false) or $this->user['login_hash'] === $login_hash))
			{
				$usergroups = array(-10); // logged in usergroup
				$usergroups = array_merge($usergroups, array_keys($this->user->usergroup));
				$this->user->usergroup[-10] = (object) array();

				$acl_tmp = \Model_Acl::find('all',
					array(
						'select' => array('slug'),
						'where' => array(
							array('usergroup_id', 'IN', $usergroups),
							'or' => array('user_id', 'IN', array( (int) $this->user->id))
						),
					)
				);
				foreach($acl_tmp as $v):
					$acls[] = $v->slug;
				endforeach;

				// always_user_allowed
				$acls_user = \Config::get('always_user_allowed');
				$acls = array_merge($acls, $acls_user);

				$this->user['allowed'] = $acls;
				return true;
			}
		}
		// not logged in, do we have remember-me active and a stored user_id?
		elseif (static::$remember_me and $user_id = static::$remember_me->get('user_id', null))
		{
			return $this->force_login($user_id);
		}

		// no valid login when still here, ensure empty session and optionally set guest_login
		$this->user = \Config::get('locomoauth.guest_login', true) ? static::$guest_login : false;
		$this->user['allowed'] = $acls;

		\Session::delete('username');
		\Session::delete('usergroups');
		\Session::delete('login_hash');

		return false;
	}

	/**
	 * Check the user exists
	 *
	 * @return  bool
	 */
	public function validate_user($username_or_email = '', $password = '')
	{
		$username_or_email = trim($username_or_email) ?: trim(\Input::post(\Config::get('locomoauth.username_post_key', 'username')));
		$password = trim($password) ?: trim(\Input::post(\Config::get('locomoauth.password_post_key', 'password')));

		if (empty($username_or_email) or empty($password))
		{
			return false;
		}

		// root and admin
		$admins = unserialize(LOCOMO_ADMINS);
		$root  = \Arr::get($admins['root'],  $username_or_email, false);
		$admin = \Arr::get($admins['admin'], $username_or_email, $root);

		// root
		if ($username_or_email == $root[0] && $password == $root[1])
		{
			\Arr::set($this->_root_info, 'username', $root[0]);
			return $this->_root_info;
		}

		// admin
		if ($username_or_email == $admin[0] && $password == $admin[1])
		{
			\Arr::set($this->_admin_info, 'username', $admin[0]);
			return $this->_admin_info;
		}

		// others
		$password = $this->hash_password($password);
		$user = \Model_Usr::find('first', array(
			'where' => array(
				array('password', '=', $password),
				array('created_at', '<=', date('Y-m-d H:i:s')),
				array(
					array('expired_at', '>=', date('Y-m-d H:i:s')),
					'or' => array('expired_at', 'is', null),
				),
				array(
					array('username', '=', $username_or_email),
					'or' => array('email', '=', $username_or_email),
				)),
			)
		);

		return $user ?: false;
	}

	/**
	 * login()
	 */
	public function login($username_or_email = '', $password = '')
	{
		// reject banned user
		if ( ! static::check_deny(\Input::post("username")))
		{
			$user_ban_setting = \Config::get('user_ban_setting');
			$limit_count = $user_ban_setting ? $user_ban_setting['limit_count'] : 3 ;
			$limit_time  = $user_ban_setting ? $user_ban_setting['limit_time'] : 300 ;
			\Session::set_flash('error', "{$limit_count}回のログインに失敗したので、{$limit_time}秒間ログインはできません。");
			return false;
		}

		// validate_user
		if ( ! ($this->user = $this->validate_user($username_or_email, $password)))
		{
			$this->user = \Config::get('locomoauth.guest_login', true) ? static::$guest_login : false;
			\Session::delete('username');
			\Session::delete('usergroups');
			\Session::delete('login_hash');
			return false;
		}

		// register so Auth::logout() can find us
		\Auth::_register_verified($this);

		// set session
		\Session::set('username', $this->user['username']);
		\Session::set('login_hash', $this->create_login_hash());
		if (isset($this->user->usergroup))
		{
			// other users
			\Session::set('usergroups', array_keys($this->user->usergroup));
		}
		else
		{
			// admin
			\Session::set('usergroups', $this->user['usergroups']);
		}
		static::add_user_log($username_or_email, $password, true);
		return true;
	}

	/**
	 * Force login user
	 *
	 * @param   string
	 * @return  bool
	 */
	public function force_login($user_id = '')
	{
		if (empty($user_id))
		{
			return false;
		}

		if ($user_id == -2)
		{
			$this->user = static::$_root;
		}

		if ($user_id == -1)
		{
			$this->user = static::$_admin;
		}

		$this->user = $this->user ? $this->user : \Model_Usr::find($user_id) ;

		if ($this->user == false)
		{
			$this->user = \Config::get('locomoauth.guest_login', true) ? static::$guest_login : false;
			\Session::delete('username');
			\Session::delete('usergroups');
			\Session::delete('login_hash');
			return false;
		}

		\Session::set('username', $this->user['username']);
		\Session::set('usergroups', array_keys($this->user->usergroup));
		\Session::set('login_hash', $this->create_login_hash());
		return true;
	}

	/**
	 * Logout user
	 *
	 * @return  bool
	 */
	public function logout()
	{
		$this->user = \Config::get('locomoauth.guest_login', true) ? static::$guest_login : false;
		\Session::delete('username');
		\Session::delete('usergroups');
		\Session::delete('login_hash');
		return true;
	}

	/**
	 * Creates a temporary hash that will validate the current login
	 *
	 * @return  string
	 */
	public function create_login_hash()
	{
		if (empty($this->user))
		{
			throw new \SimpleUserUpdateException('User not logged in, can\'t create login hash.', 10);
		}

		$last_login = \Date::forge()->get_timestamp();
		$login_hash = sha1(\Config::get('locomoauth.login_hash_salt').$this->user['username'].$last_login);

		$admins = unserialize(LOCOMO_ADMINS);
		if (
			array_key_exists($this->user['username'], $admins['root']) ||
			array_key_exists($this->user['username'], $admins['admin'])
		)
		{
			// 管理者
			\DB::delete('lcm_usr_admins')
				->where(array(array('username', '=', $this->user['username'])))
				->execute();
			\DB::insert('lcm_usr_admins')
				->set(array(
					'username'   => $this->user['username'],
					'user_id'    => $this->user['id'],
					'last_login_at' => date('Y-m-d H:i:s', $last_login),
					'login_hash' => $login_hash,
				))
				->execute();
		}
		else
		{
			// 普通のユーザ
			\DB::update('lcm_usrs')
				->set(array('last_login_at' => date('Y-m-d H:i:s', $last_login), 'login_hash' => $login_hash))
				->where('username', '=', $this->user['username'])
				->execute();
		}

		$this->user['login_hash'] = $login_hash;

		return $login_hash;
	}

	/*
	 * get_groups()
	 */
	public function get_groups()
	{
		if (isset(static::instance()->user->usergroup))
		{
			return array_keys(static::instance()->user->usergroup);
		}
		elseif (isset(static::instance()->user['usergroups']))
		{
			return static::instance()->user['usergroups'];
		}
		return array();
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
		if ($account == null) return false;
		$user_ban_setting = \Config::get('user_ban_setting');
		$limit_deny_time  = $user_ban_setting ? $user_ban_setting['limit_deny_time'] : 10 ;
		$limit_count      = $user_ban_setting ? $user_ban_setting['limit_count'] : 3 ;

		$list = \DB::select()->from("lcm_usr_logs")
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
	 * @param type $username
	 * @param type $password
	 * @param type $status
	 * @return boolean
	 * by shimizu@hinodeya at bems
	 */
	public function add_user_log($username = null, $password = null, $status = false)
	{
		if ($username == null || $password == null) return false;
		$password = $this->hash_password($password);

		// 設定値
		$user_ban_setting = \Config::get('user_ban_setting');
		$limit_time  = 10 ;
		$limit_count = $user_ban_setting ? $user_ban_setting['limit_count'] : 3 ;

		// 既にデータがあるかどうか
		$list = \DB::select()->from("lcm_usr_logs")
						//->where("login_id", $username)
						->where("status", 0)
						->where("ipaddress", $_SERVER["REMOTE_ADDR"])
						->where("add_at", ">=", \DB::expr("NOW() - INTERVAL ".$limit_time." SECOND"))
						->limit(1)
						->order_by("add_at", "DESC")
						->execute()->as_array();

		// データがあればカウントアップ
		if (count($list) && ! $status)
		{
			\DB::update("lcm_usr_logs")->value("count", $list[0]['count'] + 1)
					->where("loginlog_id", $list[0]['loginlog_id'])
					->execute();

			// 回数が一定以上あればfalseを返却
			if ($limit_count <= $list[0]['count'] + 1)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			// 成功時データを追加
			\DB::insert("lcm_usr_logs")
					->set(array(
						"login_id"   => $username,
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

	/**
	 * add_allowed()
	 */
	public function add_allowed($locomo_paths = array())
	{
		$locomo_paths = self::modify_locomopaths($locomo_paths);
		$this->user['allowed'] = array_merge($this->user['allowed'], $locomo_paths);
	}

	/**
	 * Get the user's ID
	 *
	 * @return  Array  containing this driver's ID & the user's ID
	 */
	public function get_user_id()
	{
		if (empty($this->user))
		{
			return false;
		}

		return array($this->id, (int) $this->user['id']);
	}

	/**
	 * Get the user's screen name
	 *
	 * @return  string
	 */
	public function get_screen_name()
	{
		if (empty($this->user))
		{
			return false;
		}

		return $this->user['username'];
	}

	/**
	 * Get the user's emailaddress
	 *
	 * @return  string
	 */
	public function get_email()
	{
		return $this->get('email', false);
	}

	/**
	 * Getter for user data
	 *
	 * @param  string  name of the user field to return
	 * @param  mixed  value to return if the field requested does not exist
	 *
	 * @return  mixed
	 */
	public function get($field, $default = null)
	{
		if (isset($this->user[$field]))
		{
			return $this->user[$field];
		}
		elseif (isset($this->user['profile_fields']))
		{
			return $this->get_profile_fields($field, $default);
		}

		return $default;
	}

	/**
	 * Get the user's profile fields
	 *
	 * @return  Array
	 */
	public function get_profile_fields($field = null, $default = null)
	{
		if (empty($this->user))
		{
			return false;
		}

		if (isset($this->user['profile_fields']))
		{
			is_array($this->user['profile_fields']) or $this->user['profile_fields'] = (@unserialize($this->user['profile_fields']) ?: array());
		}
		else
		{
			$this->user['profile_fields'] = array();
		}

		return is_null($field) ? $this->user['profile_fields'] : \Arr::get($this->user['profile_fields'], $field, $default);
	}
}
