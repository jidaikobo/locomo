<?php
namespace Locomo;
class Controller_Scffld_Helper_Output_Controller extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name)
	{
		// replace
		$val = static::fetch_temlpate('output_controller.php');
		$val = self::replaces($name, $val);
		return $val;
	}
}
