<?php
namespace Locomo;
class Model_Format_Excel_Eav extends Model_Base {

	protected static $_table_name = 'lcm_format_eav';

	protected static $_properties = array(
		'id',
		'format_id',
		'key',
		'value',
	);

	protected static $_belongs_to = array(
		'format' => array(
			'key_from' => 'format_id',
			'model_to' => '\Locomo\Model_format_Excel_Format',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);
}


