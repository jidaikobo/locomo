<?php
//has manyなどのテストコード。失敗中。

namespace User;

class Model_User extends \Locomo\Model_User_Abstract
{
	protected static $_has_one = array(
		//memo
		'memo' => array(
			'model_to' => '\Meta\Model_Meta',
			'key_from' => 'id',
			'key_to' => 'controller_id',
			'conditions' => array(
				'where' => array(
					array('controller', '=', 'user'),
					array('meta_key', '=', 'memo'),
				),
			),
		),
	) ;
	protected static $_has_many = array(
		//userown
		'userown' => array(
			'model_to' => '\Meta\Model_Meta',
			'key_from' => 'id',
			'key_to' => 'controller_id',
			'conditions' => array(
				'where' => array(
					array('controller', '=', 'user'),
					array('meta_key', '=', 'userown'),
				),
			),
		),
		//phones
		'phones' => array(
			'model_to' => '\Meta\Model_Meta',
			'key_from' => 'id',
			'key_to' => 'controller_id',
			'conditions' => array(
				'where' => array(
					array('controller', '=', 'user'),
					array('meta_key', '=', 'phones'),
				),
			),
		),
	) ;

	/**
	 * hash()
	 */
	private static function hash($str)
	{
		return md5($str);
	}

	/**
	 * get_userinfo()
	 * いまはidだけとってるが、あとでユーザ名もとる
	 */
	public static function get_userinfo($account = null, $password = null)
	{
		if($account == null || $password == null) return false;

		//query
		$q = \DB::select('id');
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
