<?php
namespace Scaffold;
class Helper_Config extends Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmd_orig)
	{
		//vals
		$cmds = explode(' ', $cmd_orig);
		$nicename = self::get_nicename(array_shift($cmds));

		//template
		$val = static::fetch_temlpate('config.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###nicename###', $nicename, $val);
		return $val;
	}

}
