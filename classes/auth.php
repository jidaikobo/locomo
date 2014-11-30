<?php
namespace Locomo;
class Auth extends \Auth\Auth
{
	/**
	 * is_admin()
	 */
	public static function is_admin()
	{
		if ( ! $id = \Auth::instance()->get('id')) return false;
		return ($id <= -1);
	}

	/**
	 * is_root()
	 */
	public static function is_root()
	{
		if ( ! $id = \Auth::instance()->get('id')) return false;
		return ($id === -2);
	}
}
