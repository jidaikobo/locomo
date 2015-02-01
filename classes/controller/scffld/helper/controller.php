<?php
namespace Locomo;
class Controller_Scffld_Helper_Controller extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmd_orig)
	{
		//nicename
		$cmds = explode(' ', $cmd_orig);
		$nicename = self::get_nicename(array_shift($cmds));

		// replace
		$val = static::fetch_temlpate('controller.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###NICENAME###', $nicename, $val);
		return $val;
	}
}
