<?php
namespace Locomo;
class Presenter_Usr_Edit extends \Presenter_Base
{
	/**
	 * reset_paswd_form()
	 * @return obj instanceof \Form
	 */
	public static function reset_paswd_form($mode, $obj)
	{
		$config = \Config::load('form_search', 'reset_paswd', true, true);
		$form = \Fieldset::forge('reset_paswd', $config);

		// 管理者がすべてのパスワードを知ることのできるサイトの場合
		$is_admin_knows_password = \Config::get('is_admin_knows_password', false);
		$notice = $is_admin_knows_password ? '<div><strong>このサイトはサイト管理者がパスワードを把握する仕様になっています。</strong>この設定はシステム管理者のみ変更できます。</div>' : '';

		// 検索
		if ($mode == 'bulk')
		{
			$form->add(
					'description',
					'説明',
					array('type' => 'text')
				)
				->set_template('<div>ユーザ全員のパスワードをリセットし、メールを送信します。</div>'.$notice);
		} else {
			$form->add(
					'description',
					'説明',
					array('type' => 'text')
				)
				->set_template('<div>パスワードリセットすると、強制的にパスワードを新規登録し、登録メールアドレス宛に新しいパスワードが送付されます。</div>');
		}

		// generate password
		$pswd = substr(md5(microtime()), 0, 8);
		$form->add('password', '', array('type' => 'hidden', 'value' => $pswd));

		$form->add('submit', '', array('type' => 'submit', 'value' => 'パスワードをリセットする', 'class' => 'button primary'))->set_template('<div class="submit_button">{field}</div>');

		return $form;
	}

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
		if (
			! \Auth::is_admin() &&
			\Request::main()->action != 'create'
		)
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
				array(
					'lcm_usr_password' =>
					function ($password) use ($obj)
					{
						// ユーザ名とパスワードが一緒だったらban
						if (isset($obj) && is_object($obj) ) {
							if ($password == @$obj->username)
							{
								Validation::active()->set_message('lcm_usr_password', 'ユーザ名とパスワードに同じものを使用しないでください。');
								return false;
							}
						}
						// あまりに連続した同じ文字が続いている場合ban
						if (preg_match('/([\d\w])\1{6}/', $password))
						{
							Validation::active()->set_message('lcm_usr_password', '強度が低いパスワードです。');
							return false;
						}
					}
				)
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
		if (
			! \Auth::is_admin() &&
			\Request::main()->action != 'create'
		)
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
			\Model_Usr::$_mm_delete_else = false;

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
