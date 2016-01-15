<?php
namespace Locomo;
class Model_Usr extends Model_Base_Soft
{
	/**
	 * vals
	 */
	protected static $_table_name = 'lcm_usrs';
	public static $_creator_field_name = 'id';

	// $_conditions
	protected static $_conditions = array(
		'order_by' => array('id' => 'desc'),
	);

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'username' => array(
			'lcm_role' => 'subject',
			'label' => 'ユーザ名',
			'form' => array(
				'type' => 'text',
				'size' => 20,
				'class' => 'username',
				'description' => '半角英数字、ハイフン、アンダーバー、ドットで構成してください',
			),
			'validation' => array(
				'required',
				'max_length' => array(50),
				'valid_string' => array(array('alpha','numeric','dot','dashes')),
				'unique' => array("lcm_usrs.username"),
			),
		),
		'display_name' => array(
			'label' => '表示名',
			'form' => array('type' => 'text', 'size' => 20),
			'validation' => array(
				'required',
				'max_length' => array(255),
			),
		),
		'email' => array(
			'label' => 'メールアドレス',
			'form' => array('type' => 'text', 'size' => 40),
			'validation' => array(
				'required',
				'valid_email',
				'max_length' => array(255),
			),
		),
		'main_usergroup_id' => array(
			'label' => '代表ユーザグループ',
		),
		'password' => array(
			'label' => 'パスワード',
			'form' => array('type' => 'password', 'size' => 20, 'placeholder'=>'新規作成／変更する場合は入力してください'),
			'validation' => array(
				'min_length' => array(8),
				'max_length' => array(50),
				'match_field' => array('confirm_password'),
				'valid_string' => array(array('alpha','numeric','dot','dashes')),
			),
			'default' => '',
		),
		'is_visible' => array(
			'label' => '可視属性',
			'form' => array(
				'type' => 'hidden',
				'options' => array('0' => '不可視', '1' => '可視')
			),
			'default' => 1,
			'validation' => array(
				'required',
			),
		),
		'last_login_at' => array(
			'label' => '最終ログイン',
			'form' => array('type' => false)
		),
		'expired_at' => array(
			'label' => '有効期日',
			'form' => array('type' => false),
			'default' => null
		),
		'created_at' => array(
			'label' => '作成日',
			'form' => array('type' => false),
			'default' => null
		),
		'login_hash' => array('form' => array('type' => false), 'default' => ''),
		'activation_key' => array('form' => array('type' => false), 'default' => null),
		'profile_fields' => array('form' => array('type' => false), 'default' => ''),
		'updated_at' => array('form' => array('type' => false), 'default' => null),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
		'creator_id' => array('form' => array('type' => false), 'default' => ''),
		'updater_id' => array('form' => array('type' => false), 'default' => ''),
	);

	/**
	 * relations
	 */
	protected static $_many_many = array(
		'usergroup' => array(
			'key_from' => 'id',
			'key_through_from' => 'user_id',
			'table_through' => 'lcm_usrs_usrgrps',
			'key_through_to' => 'group_id',
			'model_to' => '\Model_Usrgrp',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);
	protected static $_belongs_to = array(
		'main_usergroup' => array(
			'key_from' => 'main_usergroup_id',
			'model_to' => '\Model_Usrgrp',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		)
	);

	/**
	 * $_soft_delete
	 */
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	/**
	 * $_observers
	 */
	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Expired' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
		'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Locomo\Observer_Users' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Locomo\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save', 'before_delete'),
		),
	);

	/**
	 * set_search_options()
	 */
	public static function set_search_options()
	{
		// free word search
		$all = \Input::get('all') ? '%'.\Input::get('all').'%' : '' ;
		if ($all)
		{
			static::$_options['where'][] = array(
				array('username', 'LIKE', $all),
				'or' => array(
					array('email', 'LIKE', $all),
					'or' => array(
						array('display_name', 'LIKE', $all),
					)
				)
			);
		}

		// group
		$usergroup = \Input::get('usergroup', null) ;
		if ($usergroup)
		{
			static::$_options['related']['usergroup']['join_type'] = 'inner';
			static::$_options['related']['usergroup']['where'] = array(
				array('id', $usergroup),
			);
		}

		// span
		if (\Input::get('from')) static::$_options['where'][] = array('created_at', '>=', \Input::get('from'));
		if (\Input::get('to'))   static::$_options['where'][] = array('created_at', '<=', \Input::get('to'));
	}

	/**
	 * _event_before_save()
	 */
	public function _event_before_save()
	{
		// not for migration
		if (\Input::method() == 'POST')
		{
			// パスワードのハッシュ
			$password = \Input::post('password');
			if (empty($password))
			{
				// postがない場合、すなわちパスワード変更なし
				$this->password = $this->_original['password'];
			} else {
				// postがあるのでパスワードを変更
				$this->password = \Auth::hash_password($password);
			}
		} elseif ($this->password) {
			// POST以外の更新であれば生値を送ったものと見なしてハッシュ処理
			$this->password = \Auth::hash_password($this->password);

		}
	}

	/**
	 * get_display_name()
	 * @param int $id
	 * @return  string
	 */
	public static function get_display_name($id)
	{
		// find()
		if ($obj = static::find($id))
		{
			return $obj->display_name;
		// admins or empty
		} else {
			$admins = [-1 => '管理者', -2 => 'root管理者'];
			return \Arr::get($admins, $id, '');
		}
	}

	/**
	 * get_users()
	 */
	public static function get_users()
	{
		$options['select'][] = 'display_name';
//		$options['where'][] = array('is_visible', true);
		$options['where'][] = array('created_at', '<', date('Y-m-d H:i:s'));
		$options['where'][] = array(
			array('expired_at', '>', date('Y-m-d H:i:s')),
			'or' => array(
				array('expired_at', 'is', null),
			)
		);
		$users = array('none' => '選択してください');
		$users += static::find_options('display_name', $options);

		return $users;
	}

	/**
	 * get_users_by_group()
	 */
	public static function get_users_by_group($gid)
	{
		// カスタムユーザグループのため、一旦ユーザグループを取得する
		$usrgrp = \Model_Usrgrp::find('first',
										 array(
											 'related' => array('usergroup'),
											 'where'=> array(array('id', '=', $gid)),
										 )
		);

		$options = array();
		$options['related'] = 'usergroup';

		// ユーザグループがユーザグループを内包している場合は、それも取得する
		if ($usrgrp->usergroup)
		{
			$options['where'] = array(
				array('usergroup.id', 'IN', array_keys($usrgrp->usergroup)),
				'or' => array(
					array('usergroup.id', '=', $gid),
				)
			);
		} else {
			// ユーザグループを内包していなければ、普通に取得する
			$options['where'] = array(array('usergroup.id', '=', $gid));
		}

		$options['where'][] = array('created_at', '<', date('Y-m-d H:i:s'));
		$options['where'][] = array(
			array('expired_at', '>', date('Y-m-d H:i:s')),
			'or' => array(
				array('expired_at', 'is', null),
			)
		);
		$options['order_by'] =  array('username' => 'asc');

		$users = array('none' => '選択してください');
		$users += static::find_options('display_name', $options);

		return $users;
	}}
