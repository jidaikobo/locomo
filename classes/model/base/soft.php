<?php
namespace Locomo;
class Model_Base_Soft extends \Orm\Model_Soft
{
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);
}
