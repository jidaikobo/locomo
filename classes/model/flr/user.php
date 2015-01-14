<?php
namespace Locomo;
class Model_Flr_User extends \Model_Base
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
			'form' => array (), // override by \Locomo\Model_Flr::form_definition()
		),
	);
}
