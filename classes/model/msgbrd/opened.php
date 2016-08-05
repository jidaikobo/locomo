<?php
namespace Locomo;
class Model_Msgbrd_Opened extends \Model_Base
{
	// $_table_name
	protected static $_table_name = 'lcm_msgbrds_opened';

	// $_conditions
	protected static $_conditions = array();
	public static $_options = array();

	// $_properties
	protected static $_properties = array(
		'id',
		'msgbrd_id' => array('form' => array('type' => false), 'default' => 0),
		'user_id'    => array('form' => array('type' => false), 'default' => 0),
	);
}
