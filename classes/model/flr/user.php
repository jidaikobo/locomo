<?php
namespace Locomo;
class Model_Flr_User extends \Orm\Model
{
	/**
	 * vals
	 */
	protected static $_table_name = 'lcm_flr_permissions';

	/**
	 * $_properties
	 */
	public static $_properties = array(
		'id',
		'flr_id' => array(
			'form' => array (
				'type' => 'hidden',
			)
		),
		'user_id' => array (
			'label' => 'ユーザ',
			'data_type' => 'int',
			'form' => array (), // override by \Locomo\Model_Flr::form_definition() & Model_Flr_Dir::form_definition()
		),
		'is_writable' => array(
			'label' => '書き込み／削除権限',
			'form' => array(
				'type' => 'select',
				'options' => array('0' => '閲覧権限のみ', '1' => '書き込み／削除権限')
			),
			'default' => 0,
		),
	);

	// $_conditions
	public static $_conditions = array(
		'where' => array(
			array('usergroup_id', 'is', null)
		)
	);
}
