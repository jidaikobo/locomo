<?php
namespace Locomo;
class Controller_Scffld_Helper_Lang extends Controller_Scffld_Helper
{
	/**
	 * generate()
	 */
	public static function generate($name, $cmd_orig, $is_fallbacks = false)
	{
		// vals
		$cmd_mods = array();
		$cmds = explode(' ', $cmd_orig);
		array_shift($cmds);// remove name
		$name = ucfirst($name);
		$langs = array();

		foreach($cmds as $field)
		{
			$vals        = explode(':', $field);
			$field       = $vals[0];
			$nicename = self::get_nicename($field);
			$english  = self::remove_nicename($field);
			if ( ! $nicename) continue;
			if ($is_fallbacks)
			{
				$langs[$english] = str_replace(array('is_', '_at'), '', $english);
				$langs[$english.'_description'] = '';
			}
			else
			{
				$langs[$english] = $nicename;
				$langs[$english.'_description'] = '';
			}
		}

		if ($langs)
		{
			$langs = array(
				'controller' => array(),
				'model' => array(),
				'presenter' => array(),
				'view' => array(),) + $langs;
		}
		else
		{
			return false;
		}

		// $val
		$val = var_export($langs, true);

		$val = preg_replace("/=> \n +array \(/m",
												"=> array (",
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

		$val = '<?php '."\nreturn ".$val.';';

		return $val;
	}
}
