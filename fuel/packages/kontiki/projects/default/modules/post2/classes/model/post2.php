<?php
namespace Post2;
class Model_Post2 extends \Kontiki\Model_Crud
{
	protected static $_table_name = 'post2s';
	protected static $_primary_name = '';

	protected static $_properties = array(
		'id',
		'title',
		'body',
		'status',
		'created_at',
		'expired_at',
		'deleted_at',

 'workflow_status',
	);

	//observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);
	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
		'Kontiki\Observer\Date' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
	);
}