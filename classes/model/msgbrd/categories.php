<?php
class Model_Msgbrd_Categories extends \Model_Base_Soft
{
//	use \Model_Traits_Wrkflw;

	// $_table_name
	protected static $_table_name = 'lcm_msgbrds_categories';

	// $_conditions
	protected static $_conditions = array();

	// $_options
	public static $_options = array();

	//$_properties
	protected static $_properties = array(
		'id',
		'name' => array(
			'lcm_role' => 'subject',
			'label' => 'カテゴリ名',
			'form' => array(
				'type' => 'text',
				'size' => 20,
			),
			'validation' => array(
				'unique' => array('lcm_msgbrds_categories.name')
			),
		),
		'description' => array(
			'label' => '説明',
			'form' => array(
				'type' => 'text',
				'size' => 20,
			),
		),
		'seq' => array(
			'label' => '表示順',
			'form' => array(
				'type' => 'text',
				'size' => 5,
			),
		),
		'is_available' => array(
			'label' => '表示',
			'form' => array(
				'type' => 'select',
				'options' => array('0' => '未使用', '1' => '使用中')
			),
			'default' => 0
		),
		'deleted_at' => array(
			'form' => array(
				'type' => false
			),
			'default' => null
		),
	);


/*
	// $_has_many
	protected static $_has_many = array(
		'foo' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Foo',
			'key_to' => 'bar_id',
			'cascade_save' => true,
			'cascade_delete' => false
		)
	);
	// $_belongs_to
	protected static $_belongs_to = array(
		'foo' => array(
						'key_from' => 'foo_id',
						'model_to' => 'Model_Foo',
						'key_to' => 'id',
						'cascade_save' => true,
						'cascade_delete' => false,
					)
	);
*/

	// observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
		'Orm\Observer_UpdatedAt' => array(
				'events' => array('before_update'),
				'mysql_timestamp' => true,
			),
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Expired' => array(
				'events' => array('before_insert', 'before_save'),
				'properties' => array('expired_at'),
			),
//		't'Locomo\Observer_Workflow' => array(
//			'events' => array('before_insert', 'before_save','after_load'),
//		),
//		't'Locomo\Observer_Revision' => array(
//			'events' => array('after_insert', 'after_save', 'before_delete'),
//		),

	);

	/**
	 * set_search_options()
	 */
	public static function set_search_options()
	{
		// free word search
		$all = \Input::get('all') ? '%'.\Input::get('all').'%' : '' ;
		if ($all)
		{
			static::$_options['where'][] = array(
				array('name', 'LIKE', $all),
				'or' => array(
					array('description', 'LIKE', $all),
				) 
			);
		}
	}
}
