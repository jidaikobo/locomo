<?php
namespace Locomo;
class Model_Usrgrp_Custom extends \Model_Usrgrp
{
	// $_options
	public static $_options = array();

	// $_conditions
	protected static $_conditions = array();

	protected static $_belongs_to = array();


	/**
	 * _init()
	 */
	public static function _init()
	{
		static::$_mm_delete_else = true;

		// $_conditions
		static::$_conditions = array(
			'where' => array(
				array('is_available', true),
				array('customgroup_uid', \Auth::get('id')),
			),
			'order_by' => array('seq' => 'ACS', 'name' => 'ACS'),
		);

		// name
		\Arr::set(static::$_properties['name'], 'label', 'カスタムグループ名');

		// is_for_acl
		\Arr::set(static::$_properties['is_for_acl'], 'form', array('type' => 'hidden', 'value' => false));
		\Arr::set(static::$_properties['is_for_acl'], 'default', false);

		// customgroup_uid
		self::$_properties['customgroup_uid'] = array(
			'form' => array('type' => 'hidden'),
			'validation' => array('required'),
			'default' => \Auth::get('id'),
		);
	}

	/**
	 * _event_before_save()
	 */
	public function _event_before_save()
	{
		if (
			\Request::main()->controller == 'Controller_Usrgrp_Custom' &&
			in_array(\Request::main()->action, array('edit', 'create')) &&
			\Input::post() &&
			is_null(\Input::post('user'))
		)
		{
			unset($this->user);
		}
	}
}
