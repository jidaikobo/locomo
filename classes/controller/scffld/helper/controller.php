<?php
namespace Locomo;
class Controller_Scffld_Helper_Controller extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmd_orig, $type, $model)
	{
		//nicename
		$cmds = explode(' ', $cmd_orig);
		$nicename = self::get_nicename(array_shift($cmds));

		// replace
		$val = static::fetch_temlpate('controller.php');
		// モジュール以外では名前空間を削除
		$val = $type !== 'module' ? str_replace("namespace XXX;\n", '', $val) : $val ;
		$val = self::replaces($name, $val);
		$val = str_replace ('###NICENAME###', $nicename, $val);

		return $val;
	}
}
