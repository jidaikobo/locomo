<?php
namespace Locomo;
class Model_Srch extends \Model_Base_Soft
{
//	use \Model_Traits_Wrkflw;

	// $_table_name
	protected static $_table_name = 'lcm_srches';

	// $_conditions
	protected static $_conditions = array();

	// $_options
	public static $_options = array();

	// $_properties
	protected static $_properties =
	array (
		'id',
		'title' => array (
			'data_type' => 'varchar',
			'form' => array (
				'type' => 'text',
				'class' => 'varchar',
			),
			'validation' => array (
				'required',
			),
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
		),
		'pid' => array (
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
		'url' => array (
			'data_type' => 'text',
			'form' => array (
				'type' => 'text',
				'size' => 0,
				'class' => 'text',
			),
			'validation' => array (
				'required',
			),
		),
		'search' => array (
			'data_type' => 'text',
			'form' => array (
				'type' => 'textarea',
				'rows' => 5,
				'class' => 'text',
			),
			'validation' => array (
				'required',
			),
		),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
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

	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
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
		// static::$_authorize_methods[] = 'auth_srch';

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
		$is_or = \Input::get('and_srch') ? false : true ;

		// free word search
		$all = \Input::get('all', '') ;
		if ($all)
		{
			$all = mb_convert_kana($all, "asKV");
			$alls = explode(' ', $all);
			$whr = array();

			foreach ($alls as $v)
			{
				if ($is_or)
				{
					$whr = static::add_or($whr, array('search', 'LIKE', '%'.$v.'%'));
				}
				else
				{
					$whr[] = array('search', 'LIKE', '%'.$v.'%');
				}
			}
			static::$_options['where'][] = $whr;
		}
		else
		{
			static::$_options['where'][] = array('id' => null);
		}

	}
}
