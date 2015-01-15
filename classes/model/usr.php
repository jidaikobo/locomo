<?php
namespace Locomo;
class Model_Usr extends Model_Base
{
	/**
	 * vals
	 */
	protected static $_table_name = 'lcm_usrs';
	public static $_subject_field_name = 'username';
	public static $_creator_field_name = 'id';

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'username' => array(
			'label' => 'ユーザ名',
			'form' => array('type' => 'text', 'size' => 20, 'class' => 'username'),
			'validation' => array(
				'required',
				'max_length' => array(50),
				'valid_string' => array(array('alpha','numeric','dot','dashes')),
			),
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
			'form' => array('type' => 'text', 'size' => 20),
			'validation' => array(
				'required',
				'valid_email',
				'max_length' => array(255),
			),
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
		'last_login_at',
		'login_hash',
		'activation_key',
		'profile_fields',
		'expired_at' => array('form' => array('type' => false), 'default' => null),
		'creator_id' => array('form' => array('type' => false), 'default' => -1),
		'updater_id' => array('form' => array('type' => false), 'default' => -1),
		'created_at' => array('form' => array('type' => false), 'default' => null),
		'updated_at' => array('form' => array('type' => false), 'default' => null),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
	);

	/**
	 * $_many_many
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
		'Locomo\Observer_Password' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Locomo\Observer_Users' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Locomo\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save', 'before_delete'),
		),
	);

	//$_conditions
	public static $_conditions = array(
		'order_by' => array('id' => 'asc'),
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
//		if (static::$_cache_form_definition && $obj == null) return static::$_cache_form_definition;
		$id = isset($obj->id) ? $obj->id : '';

		//forge
		$form = parent::form_definition($factory, $obj);

		// banned user names - same as administrators
		$alladmins = unserialize(LOCOMO_ADMINS);
		$roots     = array_keys(\Arr::get($alladmins, 'root', array()));
		$admins    = array_keys(\Arr::get($alladmins, 'admin', array()));
		$allnames  = array_unique(array_merge($roots, $admins));

		//username
		$form->field('username')
			->add_rule('unique', "lcm_usrs.username.{$id}");

		//usergroups
		$options = \Model_Usrgrp::get_options(array('where' => array(array('is_available', true))), 'name');
		$checked = is_object($obj->usergroup) ? array_keys($obj->usergroup) : $obj->usergroup;
		$form->add(
				'usergroup',
				'ユーザグループ',
				array('type' => 'checkbox', 'options' => $options)
			)
			->set_value(array_keys($obj->usergroup));

		// password
		$form->field('password')->set_value('');

		//管理者以外は旧パスワードを求める
		if ( ! \Auth::is_admin()):
			$form->add(
					'old_password',
					'旧パスワード',
					array('type' => 'password', 'size' => 20, 'placeholder'=>'旧パスワードを入れてください')
				)
				->set_value('')
				->add_rule('required')
				->add_rule('min_length', 8)
				->add_rule('max_length', 50)
				->add_rule('match_password', "lcm_usrs.password.{$id}")
				->add_rule('valid_string', array('alpha','numeric','dot','dashes',));
		endif;

		//password
		$form->field('password')
			->add_rule('require_once', "lcm_usrs.password.{$id}");
	
		//confirm_password
		$form->add(
				'confirm_password',
				'確認用パスワード',
				array('type' => 'password', 'size' => 20)
			)
			->set_value('')
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',));

		//email
		$form->field('email')
			->add_rule('unique', "lcm_usrs.email.{$id}");

		//created_at
		$form->field('created_at')
			->set_label('作成日')
			->set_type('text')
			->set_attribute('placeholder', date('Y-m-d H:i:s'))
			->add_rule('non_zero_datetime');

		//expired_at
		$form->field('expired_at')
			->set_label('有効期日')
			->set_type('text')
			->set_attribute('placeholder', date('Y-m-d H:i:s'))
			->add_rule('non_zero_datetime');

		//is_visible
		if (\Auth::is_admin()):
			$form->field('is_visible')->set_type('select');
		endif;

//		static::$_cache_form_definition = $form;
		return $form;
	}
}
