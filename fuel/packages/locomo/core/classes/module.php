<?php
namespace Locomo_Core;
class Module extends \Fuel\Core\Module
{
	public static function get_exists()
	{
		$finded = array();
		foreach(\Config::get('module_paths') as $path):
			foreach (glob($path.DS."*") as $modpath):
				$module = basename($modpath);
				if( ! is_dir($path) || in_array($module, $finded)) continue;
				$retvals[$module] = str_replace('//','/',$modpath);
				$finded[] = $module;
			endforeach;
		endforeach;
		return $retvals;
	}
}
