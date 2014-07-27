<?php
namespace Kontiki;
abstract class Model_User extends \Kontiki\Model
{
	protected static $_table_name = 'users';

	protected static $_properties = array(
		'id',
		'user_name',
		'password',
		'email',
		'activation_key',
		'status',
		'last_login_at',
		'deleted_at',
		'created_at',
		'expired_at',
		'updated_at',
		'creator_id',
		'modifier_id',
	);

	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
		'Kontiki_Observer\Password' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Kontiki_Observer\Date' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
		'Kontiki_Observer\Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
	);

	/**
	 * validate()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function validate($factory, $id = '')
	{
		$val = \Kontiki\Validation::forge($factory);

		//user_name
		$val->add('user_name', 'ユーザ名')
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',))
			->add_rule('unique', "users.user_name.{$id}");

		//confirm_password
		$val->add('confirm_password', '確認用パスワード')
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',));

		//password
		$val->add('password', 'パスワード')
			->add_rule('require_once', "users.password.{$id}")
			->add_rule('min_length', 8)
			->add_rule('max_length', 50)
			->add_rule('match_field', 'confirm_password')
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',));

		//password
		$val->add('email', 'メールアドレス')
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('max_length', 255)
			->add_rule('unique', "users.email.{$id}");

		return $val;
	}

	/**
	 * find_item()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function find_item($id = null)
	{
		//parent
		$item = parent::find_item($id);

		//現在のユーザが所属するグループ
		if($item):
			$now = date('Y-m-d H:i:s', time());
			$user_id = intval($item->id);
			$q = \DB::select('usergroup_id');
			$q->from('users_usergroups_r');
			$q->join('usergroups');
			$q->on('users_usergroups_r.usergroup_id', '=', 'usergroups.id');
			$q->where('users_usergroups_r.user_id', $user_id);
			$q->where('usergroups.created_at', '<=', $now);
			$q->where('usergroups.expired_at', '>=', $now);
			$q->where('usergroups.deleted_at', '=', null);
			$resuls = $q->execute()->as_array();
			$item->usergroups = $resuls ? \Arr::flatten_assoc($resuls) : array();
		endif;

		return $item;
	}

	/**
	 * hash()
	 */
	private static function hash($str)
	{
		return md5($str);
	}

	/**
	 * get_userinfo()
	 */
	public static function get_userinfo($account = null, $password = null)
	{
		if($account == null || $password == null) return false;

		//query
		$q = \DB::select('id','user_name');
		$q->from('users');
		$q->where('password', self::hash($password));
		$q->where('deleted_at', '=', null);
		$q->where('created_at', '<=', date('Y-m-d H:i:s'));
		$q->where('expired_at', '>=', date('Y-m-d H:i:s'));
		$q->and_where_open();
			$q->where('user_name', '=', $account);
			$q->or_where('email', '=', $account);
		$q->and_where_close();
		return $q->execute()->current();
	}

	/**
	 * get_usergroups()
	 */
	public static function get_usergroups($user_id = null)
	{
		if($user_id == null) return false;

		//query
		$q = \DB::select('users_usergroups_r.usergroup_id');
		$q->distinct();
		$q->from('usergroups');
		$q->from('users_usergroups_r');
		$q->where('users_usergroups_r.user_id', '=', $user_id);
		$q->where('usergroups.deleted_at', '=', null);
		$q->where('usergroups.deleted_at', '=', null);
		$q->where('usergroups.created_at', '<=', date('Y-m-d H:i:s'));
		$q->where('usergroups.expired_at', '>=', date('Y-m-d H:i:s'));
		return \Arr::flatten_assoc($q->execute()->as_array());
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
		//var_dump($list);exit;

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
