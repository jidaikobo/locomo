<?php
namespace User;
class Model_User extends \Locomo\Model_Base
{
	protected static $_table_name = 'users';

	public static $_creator_field_name = 'id';

	protected static $_properties = array(
		'id',
		'user_name',
		'display_name',
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

	protected static $_many_many = array(
		'usergroup' => array(
			'key_from' => 'id',
			'key_through_from' => 'user_id',
			'table_through' => 'usergroups_r',
			'key_through_to' => 'group_id',
			'model_to' => '\User\Model_Usergroup',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
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
		'Locomo\Observer_Password' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Locomo\Observer_Expired' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
		'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
	);

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory, $obj = null, $id = '')
	{
		$form = \Fieldset::forge('form', \Config::get('form'));

		//user_name
		$form->add(
				'user_name',
				'ユーザ名',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->user_name)
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',))
			->add_rule('unique', "users.user_name.{$id}");

		//display_name
		$form->add(
				'display_name',
				'表示名',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->display_name)
			->add_rule('required')
			->add_rule('max_length', 255);

		//usergroups
		$opt = \User\Model_Usergroup::get_option_options('usergroup');
		$usergroups = \User\Model_Usergroup::get_options($opt['option'], $opt['label']);
		$checked = isset($obj->usergroup) ? array_keys($obj->usergroup) : array();
		$form->add(
				'usergroup',
				'ユーザグループ',
				array('type' => 'checkbox', 'options' => $usergroups)
			)
			->set_value($checked);

		//password
		$form->add(
				'password',
				'パスワード',
				array('type' => 'text', 'size' => 20, 'placeholder'=>'新規作成／変更する場合は入力してください')
			)
			->set_value('')
			->add_rule('require_once', "users.password.{$id}")
			->add_rule('min_length', 8)
			->add_rule('max_length', 50)
			->add_rule('match_field', 'confirm_password')
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',));

		//confirm_password
		$form->add(
				'confirm_password',
				'確認用パスワード',
				array('type' => 'text', 'size' => 20)
			)
			->set_value('')
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',));

		//email
		$form->add(
				'email',
				'メールアドレス',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->email)
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('max_length', 255)
			->add_rule('unique', "users.email.{$id}");

		//status
		$form->add(
				'status',
				'status',
				array('type' => 'hidden')
			)
			->set_value(@$obj->status);

		//created_at
		$form->add(
				'created_at',
				'作成日',
				array('type' => 'text', 'size' => 20, 'placeholder' => date('Y-m-d H:i:s'))
			)
			->set_value(@$obj->created_at);
//未来の日付を入れると、予約項目になります。

		//email
		$form->add(
				'deleted_at',
				'削除日',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->deleted_at);

		return $form;
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

		if($item):
			$item->usergroups = \Option\Model_Option::get_selected_options('usergroups', $id);
		endif;

		return $item;
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
