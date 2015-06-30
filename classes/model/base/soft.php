<?php
namespace Locomo;
class Model_Base_Soft extends \Orm\Model_Soft
{
	use \Model_Traits_Base;

	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);
}
