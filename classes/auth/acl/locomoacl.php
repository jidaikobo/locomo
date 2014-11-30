<?php
namespace Locomo;
class Auth_Acl_Locomoacl extends \Auth_Acl_Driver
{
	protected static $_valid_roles = array();

	public static function _init()
	{
//		static::$_valid_roles = array_keys(\Config::get('locomoauth.roles'));
	}

	public function roles()
	{
		//Auth_Acl_Locomoaclではroleを使わない
		return static::$_valid_roles;
	}

	/**
	 * Parses a conditions string into it's array equivalent
	 * @rights	mixed	conditions array or string
	 * @return	array	conditions array formatted as array(controller, action)
	 *
	 */
	public static function _parse_conditions($rights)
	{
		if ( ! is_array($rights))
		{
			if ( ! is_string($rights) or strpos($rights, '/') === false)
			{
				throw new \InvalidArgumentException('Given rights where not formatted proppery. Formatting should be like [\\Foo]\\Controller_Foo/action. Received: '.$rights);
			}
			$rights = explode('/', $rights);
		}

		// $conditions
		$conditions = array(
			'controller' => $rights[0],
			'action'     => $rights[1],
		);

		return $conditions;
	}

	/*
	 * has_access()
	 * @param $condition	[array array(controller, action)|string '[\\Foo]\\Controller_Foo/bar']
	 * @param $entity	not used at \Locomo
	 * @return bool
	 */
	public function has_access($condition, Array $entity)
	{
		//no rights for no condition
		if ( ! $condition) return false;

		// admins are all allowed
		if (in_array(\Auth::get('id'), array(-1, -2))) return true;

		// parse condition to serialize
		$conditions = static::_parse_conditions($condition);
		$condition = serialize($conditions);

		return in_array($condition, \Auth::get('allowed'));
	}
}
