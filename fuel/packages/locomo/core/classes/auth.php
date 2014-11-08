<?php
namespace Locomo;
class Auth extends \Auth\Auth
{
	/**
	 * just override config path.
	 * eventually, Locomo cares about App directory, this file will be deleted.
	 */
	public static function _init()
	{
		\Config::load(PKGCOREPATH.'/config/auth', true);

		foreach((array) \Config::get('auth.driver', array()) as $driver => $config)
		{
			$config = is_int($driver)
				? array('driver' => $config)
				: array_merge($config, array('driver' => $driver));
			static::forge($config);
		}

		// Set the first (or only) as the default instance for static usage
		if ( ! empty(static::$_instances))
		{
			static::$_instance = reset(static::$_instances);
			static::check();
		}
	}

	/**
	 * is_admin()
	 */
	public static function is_admin()
	{
		if( ! $id = \Auth::instance()->get('id')) return false;
		return ($id <= -1);
	}

	/**
	 * is_root()
	 */
	public static function is_root()
	{
		if( ! $id = \Auth::instance()->get('id')) return false;
		return ($id === -2);
	}
}
