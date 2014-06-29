<?php
namespace Kontiki;
abstract class Model_Usergroup_Abstract extends \Kontiki\Model
{
	protected static $_table_name = 'usergroups';

	protected static $_properties = array(
		'id',
		'usergroup_name',
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

		//usergroup_name
		$val->add('usergroup_name', 'ユーザグループ名')
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('unique', "usergroups.usergroup_name.{$id}");

		return $val;
	}

}
