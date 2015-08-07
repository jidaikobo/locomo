<?php
namespace Locomo;
class Model_Pdf_Eav extends Model_Base {

	protected static $_table_name = 'lcm_pdf_eav';

	protected static $_properties = array(
		'id',
		'format_id',
		'key',
		'value',
	);

	protected static $_belongs_to = array(
		'format' => array(
			'key_from' => 'format_id',
			'model_to' => '\Locomo\Model_Pdf_Format',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);
}


