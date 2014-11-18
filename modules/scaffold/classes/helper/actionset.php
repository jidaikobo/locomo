<?php
namespace Scaffold;
class Helper_Actionset extends Helper
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
