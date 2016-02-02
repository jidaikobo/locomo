<?php
namespace Locomo;
class Model_Pgfl extends \Model_Base_Soft
{
//	use \Model_Traits_Wrkflw;

	// $_table_name
	protected static $_table_name = 'lcm_pgfls';

	// $_conditions
	protected static $_conditions = array();

	// $_options
	public static $_options = array();

	// $_properties
	protected static $_properties =
	array (
		'id',
		'pg_id' => array (
			'data_type' => 'int',
			'form' => array (
				'type' => 'text',
				'size' => 5,
				'class' => 'int',
			),
			'validation' => array (
				'required',
				'max_length' => array (5),
			),
		),
		'name' => array (
			'label' => '名前',
			'data_type' => 'varchar',
			'form' => array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
			'validation' => array (
				'required',
				'max_length' => array (255),
			),
			'default' => '',
		),
		'path' => array (
			'data_type' => 'varchar',
			'form' => array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
			'validation' => array (
				'required',
				'max_length' => array (255),
			),
			'default' => '',
		),
		'url' => array (
			'data_type' => 'varchar',
			'form' => array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
			'validation' => array (
				'required',
				'max_length' => array (255),
			),
			'default' => '',
		),
		'alt' => array (
			'label' => 'alt',
			'data_type' => 'varchar',
			'form' => array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
			'validation' => array (
				'required',
				'max_length' => array (255),
			),
			'default' => '',
		),
		'created_at' => array (
			'data_type' => 'datetime',
			'form' => array (
				'type' => 'text',
				'size' => 20,
				'class' => 'datetime',
			),
			'default' => null,
		),
		'deleted_at' => array (
			'form' => array (
				'type' => false,
			),
		),
		'expired_at' => array (
			'data_type' => 'datetime',
			'form' => array (
				'type' => 'text',
				'size' => 20,
				'class' => 'datetime',
			),
			'default' => null,
		),
		'creator_id' => array (
			'form' => array (
				'type' => false,
			),
		),
		'updater_id' => array (
			'form' => array (
				'type' => false,
			),
		),
	) ;

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
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Expired' => array(
				'events' => array('before_insert', 'before_save'),
				'properties' => array('expired_at'),
			),
			'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
//			'Locomo\Observer_Wrkflw' => array(
//			'events' => array('before_insert', 'before_save','after_load'),
//		),
//			'Locomo\Observer_Revision' => array(
//			'events' => array('after_insert', 'after_save', 'before_delete'),
//		),

	);

	/**
	 * _init
	 */
	 public static function _init()
	{
		// set $_authorize_methods
		// static::$_authorize_methods[] = 'auth_pgfl';

		// do something before call parent::_init()

		// parent - this must be placed at the end of _init()
		parent::_init();
	}

	/**
	 * set_search_options()
	 */
	public static function set_search_options()
	{
		// free word search
/*
		$all = \Input::get('all') ? '%'.\Input::get('all').'%' : '' ;
		if ($all)
		{
			static::$_options['where'][] = array(
				array('name', 'LIKE', $all),
				'or' => array(
					array('body', 'LIKE', $all),
					'or' => array(
						array('memo', 'LIKE', $all),
					)
				)
			);
		}
*/
	}
}
