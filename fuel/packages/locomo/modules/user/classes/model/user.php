<?php
namespace User;
class Model_User extends \Locomo\Model_Base
{
	/**
	 * vals
	 */
	protected static $_table_name = 'users';
	public static $_subject_field_name = 'username';
	public static $_creator_field_name = 'id';

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'username',
		'password',
		'email',
		'display_name',
		'last_login_at',
		'login_hash',
		'activation_key',
		'profile_fields',
		'is_visible',
		'deleted_at',
		'created_at',
		'expired_at',
		'updated_at',
		'creator_id',
		'modifier_id',
	);

	/**
	 * $_many_many
	 */
	protected static $_many_many = array(
		'usergroup' => array(
			'key_from' => 'id',
			'key_through_from' => 'user_id',
			'table_through' => 'user_usergroups',
			'key_through_to' => 'group_id',
			'model_to' => '\User\Model_Usergroup',
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
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Expired' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
		'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'User\Observer_Password' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'User\Observer_Users' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Revision\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save', 'before_delete'),
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
	public static function form_definition($factory = 'user', $obj = null)
	{
		if(static::$_cache_form_definition && $obj == null) return static::$_cache_form_definition;
		$id = isset($obj->id) ? $obj->id : '';

		//forge
		$form = \Fieldset::forge($factory, \Config::get('form'));

		// banned user names - same as administrators
		$alladmins = unserialize(LOCOMO_ADMINS);
		$roots     = array_keys(\Arr::get($alladmins, 'root', array()));
		$admins    = array_keys(\Arr::get($alladmins, 'admin', array()));
		$allnames  = array_unique(array_merge($roots, $admins));

		//username
		$form->add(
				'username',
				'ユーザ名',
				array('type' => 'text', 'size' => 20, 'class' => 'username')
			)
			->set_value(@$obj->username)
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('banned_string', $allnames)
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',))
			->add_rule('unique', "users.username.{$id}");

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

		//管理者以外は旧パスワードを求める
		if( ! \Auth::is_admin()):
			$form->add(
					'old_password',
					'旧パスワード',
					array('type' => 'password', 'size' => 20, 'placeholder'=>'旧パスワードを入れてください')
				)
				->set_value('')
				->add_rule('required')
				->add_rule('min_length', 8)
				->add_rule('max_length', 50)
				->add_rule('match_password', "users.password.{$id}")
				->add_rule('valid_string', array('alpha','numeric','dot','dashes',));
		endif;

		//password
		$form->add(
				'password',
				'パスワード',
				array('type' => 'password', 'size' => 20, 'placeholder'=>'新規作成／変更する場合は入力してください')
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
				array('type' => 'password', 'size' => 20)
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

		//created_at
		$form->add(
				'created_at',
				'作成日',
				array('type' => 'text', 'size' => 20, 'placeholder' => date('Y-m-d H:i:s'), 'class' => 'datetime')
			)
			->set_value(@$obj->created_at)
			->add_rule('non_zero_datetime');
			//未来の日付を入れると、予約項目になります。

		//deleted_at
		$form->add(
				'deleted_at',
				'削除日',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->deleted_at);

		//is_visible
		if(\Auth::is_admin()):
			$form->add(
					'is_visible',
					'可視属性',
					array('type' => 'select', 'options' => array('0' => '不可視', '1' => '可視'), 'default' => 1)
				)
				->add_rule('required')
				->set_value(@$obj->is_visible);
		else:
			$form->add(
					'is_visible',
					'可視属性',
					array('type' => 'hidden', 'default' => 1)
				)
				->add_rule('required')
				->set_value(@$obj->is_visible);
		endif;

		static::$_cache_form_definition = $form;
		return $form;
	}
}
