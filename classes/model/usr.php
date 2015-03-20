<?php
namespace Locomo;
class Model_Usr extends Model_Base
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
	public static $_options = array();

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'username' => array(
			'lcm_role' => 'subject',
			'label' => 'ユーザ名',
			'form' => array('type' => 'text', 'size' => 20, 'class' => 'username'),
			'validation' => array(
				'required',
				'max_length' => array(50),
				'valid_string' => array(array('alpha','numeric','dot','dashes')),
				'unique' => array("lcm_usrgrps.name"),
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
		if ($obj = \Model_Usr::find($id))
		{
			return $obj->display_name;
		// admins or empty
		} else {
			$admins = [-1 => '管理者', -2 => 'root管理者'];
			return \Arr::get($admins, $id, '');
		}
	}

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
		$id = isset($obj->id) ? $obj->id : '';

		// forge
		$form = parent::form_definition($factory, $obj);

		// usernameに予約語を設定
		// banned user names - same as administrators
		$alladmins = unserialize(LOCOMO_ADMINS);
		$roots     = array_keys(\Arr::get($alladmins, 'root', array()));
		$admins    = array_keys(\Arr::get($alladmins, 'admin', array()));
		$allnames  = array_unique(array_merge($roots, $admins));
		$form->field('username')
			->add_rule('banned_string', $allnames);

		// usernameの変更は管理者のみ可能
		if ( ! \Auth::is_admin())
		{
			$form->field('username')
				->set_type('hidden');
			$form->add_after(
					'display_username',
					'ユーザ名',
					array('type' => 'text', 'disabled' => 'disabled'),
					array(),
					'username'
				)
				->set_value($obj->username);
		}

		// password
		$form->field('password')
			->set_value('')
			->add_rule('require_once', "lcm_usrs.password.{$id}")
			->add_rule(
				function ($password) use ($obj)
				{
					// ユーザ名とパスワードが一緒だったらban
					if (isset($obj) && is_object($obj) ) {
						if ($password == @$obj->username)
						{
							Validation::active()->set_message('closure', 'ユーザ名とパスワードに同じものを使用しないでください。');
							return false;
						}
					}
					// あまりに連続した同じ文字が続いている場合ban
					if (preg_match('/([\d\w])\1{6}/', $password))
					{
						Validation::active()->set_message('closure', '強度が低いパスワードです。');
						return false;
					}
				}
			);

		// confirm_password
		$form->add_after(
				'confirm_password',
				'確認用パスワード',
				array('type' => 'password', 'size' => 20),
				array(),
				'password'
			)
			->set_value('')
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',));

		// 管理者以外は現在のパスワードを求める
		if ( ! \Auth::is_admin())
		{
			$form->add_after(
					'old_password',
					'現在のパスワード',
					array('type' => 'password', 'size' => 20, 'placeholder'=>'現在のパスワードを入れてください'),
					array(),
					'confirm_password'
				)
				->set_value('')
				->add_rule('required')
				->add_rule('min_length', 8)
				->add_rule('max_length', 50)
				->add_rule('match_password', "lcm_usrs.password.{$id}")
				->add_rule('valid_string', array('alpha','numeric','dot','dashes',));
		}

		// email
		$form->field('email')
			->add_rule('unique', "lcm_usrs.email.{$id}");

		// ユーザグループ取得（代表用）
		$ugrps4main = Model_Usrgrp::get_options(
			array(
				'where' => array(
					array('is_available', true),
					array('customgroup_uid', null),
					array('is_for_acl', false),
				),
				'order_by' => array('seq' => 'ASC', 'name' => 'ASC')
			),
			'name'
		);

		// 選択されているユーザグループのみに絞り込む
		$choosen = $obj->usergroup ?: array();
		foreach ($ugrps4main as $k => $v)
		{
			if ( ! array_key_exists($k, $choosen))
			{
				unset($ugrps4main[$k]);
			}
		}

		// 代表ユーザグループ
		$form->field('main_usergroup_id')
			->set_type('select')
			->set_options($ugrps4main)
			->set_value($obj->main_usergroup_id);

		// usergroup can modified by admin only 
		$options = \Model_Usrgrp::get_options(array('where' => array(array('is_available', true), array('customgroup_uid', null))), 'name');
		if (\Auth::is_admin())
		{
			$options = \Model_Usrgrp::get_options(array('where' => array(array('is_available', true), array('customgroup_uid', null))), 'name');
			$form->add_after(
					'usergroup',
					'ユーザグループ',
					array('type' => 'checkbox', 'options' => $options),
					array(),
					'email'
				)
				->set_value(array_keys($obj->usergroup));
		} else {
			$usergroup = $obj->usergroup;
			unset($usergroup[-10]); // 自分自身を参照するログインユーザーグループ
			$obj->usergroup = $usergroup;
			static::$_mm_delete_else = false;

			$usergroup_str = array();
			foreach ($usergroup as $k => $v)
			{
				$usergroup_str[] = $v->name;
			}
			$config = \Config::get('form');
			$form->add_after(
					'usergroup',
					'ユーザグループ',
					array('type' => 'text'),
					array(),
					'email'
				)
				->set_template(str_replace('{field}', join(', ', $usergroup_str), $config['field_template']));
		}

		// created_at
		$form->field('created_at')
			->set_label('作成日')
			->set_type('text')
			->set_attribute('placeholder', date('Y-m-d H:i:s'))
			->add_rule('non_zero_datetime');

		// expired_at
		$form->field('expired_at')
			->set_label('有効期日')
			->set_type('text')
			->set_attribute('placeholder', date('Y-m-d H:i:s'))
			->add_rule('non_zero_datetime');

		// is_visible and created_at
		if (\Auth::is_admin())
		{
			$form->field('is_visible')->set_type('select');
		} else {
			$form->delete('expired_at');
			$form->delete('created_at');
		}

		return $form;
	}

	/**
	 * search_form()
	*/
	public static function search_form()
	{
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge('user', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// ユーザグループ
		$options = array('' => '選択してください');
		$options+= \Model_Usrgrp::get_options(array('where' => array(array('is_available', true)), 'order_by' => array('name')), 'name');
		$form->add(
				'usergroup',
				'ユーザグループ',
				array('type' => 'select', 'options' => $options)
			)
			->set_value(\Input::get('usergroup'));

		// 登録日 - 開始
		$form->add(
				'from',
				'登録日',
				array(
					'type'        => 'text',
					'value'       => \Input::get('from'),
					'id'          => 'registration_date_start',
					'class'       => 'date',
					'placeholder' => date('Y-n-j', time() - 86400 * 365),
					'title'       => '登録日 開始 ハイフン区切りで入力してください',
				)
			)
			->set_template('
				<div class="input_group">
				<h2>登録日</h2>
				{field}&nbsp;から
			');

		// 登録日 - ここまで
		$form->add(
				'to',
				'登録日',
				array(
					'type'        => 'text',
					'value'       => \Input::get('to'),
					'id'          => 'registration_date_end',
					'class'       => 'date',
					'placeholder' => date('Y-n-j'),
					'title'       => '登録日 ここまで ハイフン区切りで入力してください',
				)
			)
			->set_template('
				{field}</div><!--/.input_group-->
			');

		// wrap
		$parent = parent::search_form_base('ユーザ');
		$parent->add_after($form, 'user', array(), array(), 'opener');

		return $parent;
	}

	/**
	 * reset_paswd_form()
	*/
	public static function reset_paswd_form($mode = '')
	{
		$config = \Config::load('form_search', 'reset_paswd', true, true);
		$form = \Fieldset::forge('reset_paswd', $config);

		// 検索
		if ($mode == 'bulk')
		{
			$form->add(
					'description',
					'説明',
					array('type' => 'text')
				)
				->set_template('
					<div>ユーザ全員のパスワードをリセットし、メールを送信します。</div>
				');
		} else {
			$form->add(
					'description',
					'説明',
					array('type' => 'text')
				)
				->set_template('
					<div>パスワードリセットすると、強制的にパスワードを新規登録し、登録メールアドレス宛に新しいパスワードが送付されます。</div>
				');
		}

		// generate password
		$pswd = substr(md5(microtime()), 0, 8);
		$form->add('password', '', array('type' => 'hidden', 'value' => $pswd));

		$form->add('submit', '', array('type' => 'submit', 'value' => 'パスワードをリセットする', 'class' => 'button primary'))->set_template('<div class="submit_button">{field}</div>');

		return $form;
	}
}
