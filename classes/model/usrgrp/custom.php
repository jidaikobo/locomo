<?php
namespace Locomo;
class Model_Usrgrp_Custom extends \Model_Usrgrp
{
	// $_options and $_conditions
	public static $_options = array();
	protected static $_conditions = array();

	/**
	 * _init()
	 */
	public static function _init()
	{
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

		// parent - this must be placed at the end of _init()
		parent::_init();
	}

	/**
	 * _event_before_save()
	 */
	public function _event_before_save()
	{
		// off the checkboxes of users
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

	/**
	 * set_public_options()
	 * @param array() $exception
	 * @return array()
	 */
	public static function set_public_options($exception = array())
	{
		$options = parent::set_public_options($exception);

		// $_options
		if (empty($exception) || ! in_array('is_available', $exception))
		{
			$options['where'][] = array('is_available', '=', true);
		}
		$options['where'][] = array('customgroup_uid', '=', \Auth::get('id'));
		$options['where'][] = array('is_for_acl', '=', false);
		$options['order_by'] = array('seq' => 'ASC', 'name' => 'ASC');

		// array_merge
		static::$_options = \Arr::merge_assoc(static::$_options, $options);

		//return
		return $options;
	}

	/**
	 * find_options()
	 * ユーザグループに加えて、カスタムユーザグループを取得する
	 * get usergroups and user's custom groups
	 */
	public static function find_options($label = 'name', $options = array())
	{
		$items = static::find(
			'all',
			array(
				'where' => array(
					array('is_available', true),
					array('is_for_acl', 0),
					array(
						array('customgroup_uid', \Auth::get('id')),
						'or' => array('customgroup_uid', 'is', null)
					)
				),
				'order_by' => array('seq' => 'ASC', 'name' => 'ASC'),
			)
		);
		$items = \Arr::assoc_to_keyval($items, 'id', 'name');
		return $items;
	}
}
