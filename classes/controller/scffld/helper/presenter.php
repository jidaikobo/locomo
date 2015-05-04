<?php
namespace Locomo;
class Controller_Scffld_Helper_Presenter extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $type, $tpl)
	{
		$val = static::fetch_temlpate('presenter_'.$tpl.'.php');
		// モジュール以外では名前空間を削除
		$val = $type !== 'module' ? str_replace("namespace XXX;\n", '', $val) : $val ;
		$val = self::replaces($name, $val);
		return $val;
	}
}
