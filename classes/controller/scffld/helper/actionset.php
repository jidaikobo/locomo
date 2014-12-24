<?php
namespace Locomo;
class Controller_Scffld_Helper_Actionset extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $realm)
	{
		$realm = basename($realm);
		$val = static::fetch_temlpate('actionset_'.$realm.'.php');
		$val = self::replaces($name, $val);
		return $val;
	}
}
