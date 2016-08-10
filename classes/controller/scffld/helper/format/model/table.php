<?php
namespace Locomo;
class Controller_Scffld_Helper_Format_Model_Table extends Controller_Scffld_Helper
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
		$val = static::fetch_temlpate('format_model_table.php');
		$val = self::replaces($name, $val);
		$val = str_replace ('###NICENAME###', $nicename, $val);
		return $val;
	}
}
