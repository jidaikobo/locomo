<?php
namespace XXX;
class Model_XXX extends \Model_Base
{
//	use \Model_Traits_Wrkflw;

	// $_table_name
	protected static $_table_name = '###TABLE_NAME###';

	// $_conditions
	protected static $_conditions = array();

	// $_options
	public static $_options = array();

	// $_properties
	protected static $_properties =
###FIELD_STR### ;

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
###DLT_FLD###
	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
###OBSRVR###
	);

	/**
	 * _init
	 */
	 public static function _init()
	{
		// set $_authorize_methods
		// static::$_authorize_methods[] = 'auth_xxx';

		// do something before call parent::_init()

		// parent - this must be placed at the end of _init()
		parent::_init();
	}

	/**
	 * _event_before_save()
	 */
	public function _event_before_save()
	{
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
