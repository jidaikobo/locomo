<?php
namespace Sample;
class Model_Hasmany extends \Locomo_Core\Model_Base
{
	protected static $_table_name = 'hasmany';

	protected static $_properties = array(
		'id',
		'name',
		'sample_id'  => array('form' => array('type' => false)),
		'created_at' => array('form' => array('type' => false)),
		'expired_at' => array('form' => array('type' => false)),
		'deleted_at' => array('form' => array('type' => false)),
	);
}

