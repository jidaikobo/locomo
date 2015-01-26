<?php
namespace Locomo;
class Model_Flr_Usergroup extends \Orm\Model
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
		'usergroup_id' => array (
			'label' => 'ユーザグループ',
			'data_type' => 'int',
			'form' => array (), // override by \Locomo\Model_Flr::form_definition() & Model_Flr_Dir::form_definition()
		),
		'is_writable' => array(
			'label' => '書き込み／削除権限',
			'form' => array(
				'type' => 'select',
				'options' => array('' => '権限を選択してください', '0' => '閲覧権限', '1' => '書き込み／削除権限')
			),
			'default' => 0,
		),
	);

	// $_conditions
	public static $_conditions = array(
		'where' => array(
			array('user_id', 'is', null)
		)
	);
}
