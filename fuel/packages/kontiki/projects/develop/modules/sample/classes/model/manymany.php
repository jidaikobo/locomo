<?php
namespace Sample;
class Model_Manymany extends \Kontiki\Model_Base
{
	protected static $_table_name = 'manymany';

	protected static $_properties = array(
		'id',
		'name',
		'created_at',
		'expired_at',
		'deleted_at',
	);
}
