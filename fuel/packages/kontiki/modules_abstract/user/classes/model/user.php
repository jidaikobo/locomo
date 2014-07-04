<?php
namespace Kontiki;

abstract class Model_User_Abstract extends \Kontiki\Model
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
}
