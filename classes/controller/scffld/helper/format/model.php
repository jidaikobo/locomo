<?php
namespace Locomo;
class Controller_Scffld_Helper_Format_Model extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmd_orig)
	{

		// vals
		$cmd_mods = array();
		$cmds = explode(' ', $cmd_orig);
		array_shift($cmds);// remove name
		$name = ucfirst($name);
		$fields = array();

		foreach($cmds as $field)
		{
			$vals        = explode(':', $field);
			$field       = $vals[0];
			$nicename = self::get_nicename($field);
			$english  = self::remove_nicename($field);
			if ( ! $nicename) continue;
			$fields[$english] = $nicename;
		}

		// $val
		$val = var_export($fields, true);

		$val = preg_replace("/=> \n +array \(/m",
												"=> array (",
												$val);
		$val = preg_replace("/^/m",
															"\t",
															$val);
		$val = str_replace('  ',
											 "\t",
											 $val);
		$val = preg_replace("/array \(\n\t+?(\d+),\n\t+?\),/m",
												"array ($1),",
												$val);
		$val = str_replace("'view' => array (\n\t),\n",
											 "'view' => array (\n\t),\n\n\t// common\n",
											 $val);
		$val = preg_replace("/array \(\n\t+?(\d+),\n\t+?\),/m",
															"array ($1),",
															$val);

		$str = static::fetch_temlpate('format_model.php');
		$str = self::replaces($name, $str);
		$str = str_replace('###PDF_STR###',  $val,  $str);
		$str = str_replace('###EXCEL_STR###',  $val,  $str);
		$str = str_replace("=\n\tarray (",
											 "= array (",
											 $str);

		return $str;
	}
}
