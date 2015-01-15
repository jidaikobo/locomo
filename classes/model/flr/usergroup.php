<?php
namespace Locomo;
class Model_Flr_Usergroup extends \Model_Base
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
			'form' => array (), // override by \Locomo\Model_Flr::form_definition()
		),
	);
}
