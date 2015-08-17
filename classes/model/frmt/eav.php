<?php
namespace Locomo;
class Model_Frmt_Eav extends Model_Base_Soft
{
	protected static $_table_name = 'lcm_frmt_eav';

	protected static $_properties = array(
		'id',
		'format_id',
		'key',
		'value',
		'created_at' => array('form' => array('type' => false), 'default' => null),
		'updated_at' => array('form' => array('type' => false), 'default' => null),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
		'creator_id' => array('form' => array('type' => false), 'default' => ''),
		'updater_id' => array('form' => array('type' => false), 'default' => ''),
	);

	protected static $_belongs_to = array(
		'format' => array(
			'key_from' => 'format_id',
			'model_to' => '\Locomo\Model_Frmt',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);
}


