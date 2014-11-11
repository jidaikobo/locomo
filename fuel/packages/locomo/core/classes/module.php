<?php
namespace Locomo;
class Module extends \Fuel\Core\Module
{
	/**
	 * get_exists()
	 * get all modules
	 * @return array
	 */
	public static function get_exists()
	{
		$finded = array();
		foreach(\Config::get('module_paths') as $path):
			foreach (glob($path.DS."*") as $modpath):
				$module = strtolower(basename($modpath));
				if( ! is_dir($path) || in_array($module, $finded)) continue;
				$retvals[$module] = str_replace(DS.DS, DS, $modpath);//eliminate doubled slash
				$finded[] = $module;
			endforeach;
		endforeach;
		return $retvals;
	}

	/**
	 * get_controllers ()
	 * get all controllers of module
	 * @param string	module name
	 * @return array
	 */
	public static function get_controllers ($module = null)
	{
		if ( ! is_scalar($module)) new \InvalidArgumentException('It is necessary to set valid module name.');
		if ( ! $path = \Module::exists($module)) throw new \InvalidArgumentException('Module not found.');
		$path.= 'classes/controller';
		if ( ! is_dir($path)) return false;

		// generate controller class name
		return \Inflector::dir_to_ctrl($path);
	}
}
