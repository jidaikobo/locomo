<?php
namespace Locomo;
class Controller_Scffld_Helper_Actionset extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name)
	{
		$val = static::fetch_temlpate('actionset.php');
		$val = self::replaces($name, $val);
		return $val;
	}
}
