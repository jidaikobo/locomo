<?php
namespace Office;
class Model_Subject extends \Locomo\Model_Base
{
//	use \Workflow\Traits_Model_Workflow;

	protected static $_table_name = 'support_contribute_subject';
	public static $_subject_field_name = 'SOME_TRAITS_USE_SUBJECT_FIELD_NAME';

	protected static $_properties = array(
		'id',
		'name',
		'order',
		'is_visible',
		'created_at',
		'updated_at',
		'deleted_at',
		'is_support'
		// 'workflow_status',
	);

	protected static $_depend_modules = array();

	/**
	 * $_option_options - see sample at \User\Model_Usergroup
	 */
	public static $_option_options = array();

/*
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
		'Locomo\Observer_Expired' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
//		'Workflow\Observer_Workflow' => array(
//			'events' => array('before_insert', 'before_save','after_load'),
//		),
	);
*/

}


