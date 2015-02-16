<?php
namespace Locomo;
class Auth_Acl_Locomoacl extends \Auth_Acl_Driver
{
	protected static $_valid_roles = array();

	public static function _init()
	{
	}

	public function roles()
	{
		return static::$_valid_roles;
	}

	/*
	 * has_access()
	 * @param $condition	[array array(controller, action)|string '[\\Foo]\\Controller_Foo/bar']
	 * @param $entity	not used at \Locomo
	 * @return bool
	 */
	public function has_access($condition, Array $entity)
	{
		// no rights for no condition
		if ( ! $condition) return false;

		$condition = \Inflector::add_head_backslash($condition);

		// event
		// to do nothing call back must return non bool value
		if (\Event::instance()->has_events('locomo_has_access'))
		{
			$flag = \Event::instance()->trigger('locomo_has_access', $condition);
			if ($flag === '1') return true;
			if ($flag === '0') return false;
		}

		// check related controller first - for speed up
		if (
			! in_array(\Auth::get('id'), array(-1, -2)) &&
			! in_array(\Inflector::get_controllername($condition), \Auth::get('related_controllers')))
		{
			return false;
		}

		// check class_exists
		$module = \Inflector::get_modulename($condition);
		$module && \Module::loaded($module) == false and \Module::load($module);

		// controller and action
		if ( ! \Util::method_exists($condition)) return false;

		// admins are all allowed even if class and method is not exist.
		if (in_array(\Auth::get('id'), array(-1, -2))) return true;

		return in_array($condition, \Auth::get('allowed'));
	}
}
