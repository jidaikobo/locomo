<?php
namespace Locomo;
class Controller_Scffld_Helper_Actionset extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $type)
	{
		$val = static::fetch_temlpate('actionset.php');
		// モジュール以外では名前空間を削除
		$val = $type !== 'all' ? str_replace("namespace XXX;\n", '', $val) : $val ;
		$val = self::replaces($name, $val);
		return $val;
	}
}
