<?php
class Presenter_Usr_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

		$id = isset($obj->id) ? $obj->id : '';

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
		$ugrps4main = Model_Usrgrp::find_options(
			'name',
			array(
				'where' => array(
					array('is_available', true),
					array('customgroup_uid', null),
					array('is_for_acl', false),
				),
				'order_by' => array('seq' => 'ASC', 'name' => 'ASC')
			)
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

		if ($ugrps4main)
		{
			// 代表ユーザグループ
			$form->field('main_usergroup_id')
				->set_description('権限用ユーザグループは対象にならないので、所属していても候補にされません。')
				->set_type('select')
				->set_options($ugrps4main)
				->set_value($obj->main_usergroup_id);
		}
		else
		{
			// 代表ユーザグループ
			$form->field('main_usergroup_id')
				->set_type('hidden')
				->set_value($obj->main_usergroup_id);
		}

		// usergroup can modified by admin only 
		if (\Auth::is_admin())
		{
			$options = \Model_Usrgrp::find_options('name', array('where' => array(array('is_available', true), array('customgroup_uid', 'IS', null))));
			if ($options) {
			$checked = $obj->usergroup ? array_keys($obj->usergroup) : array();
			$form->add_after(
					'usergroup',
					'ユーザグループ',
					array('type' => 'checkbox', 'options' => $options),
					array(),
					'email'
				)
				->set_value($checked);
			}
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
			->set_attribute('class', 'datetime')
			->add_rule('non_zero_datetime');

		// expired_at
		$form->field('expired_at')
			->set_label('有効期日')
			->set_type('text')
			->set_attribute('placeholder', date('Y-m-d H:i:s'))
			->set_attribute('class', 'datetime')
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
}
