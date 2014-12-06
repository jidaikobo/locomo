<?php
namespace Locomo;
class Module extends \Fuel\Core\Module
{
	public static $exist_modules = array();
	public static $exist_controllers = array();

	/**
	 * get_exists()
	 * get all modules
	 * called from \Util::get_mod_or_ctrl() by \Locomo\View::base_assign()
	 * @return array
	 */
	public static function get_exists()
	{
		//cache
		try
		{
			//root not use cache
			if (\Auth::is_root()) throw new \CacheNotFoundException();
			return \Cache::get('exist_modules');
		}
		catch (\CacheNotFoundException $e)
		{
			$finded = array();
			foreach(\Config::get('module_paths') as $path):
				foreach (glob($path.DS."*") as $modpath):
					$module = strtolower(basename($modpath));
					if ( ! is_dir($path) || in_array($module, $finded)) continue;
					$retvals[$module] = str_replace(DS.DS, DS, $modpath);//eliminate doubled slash
					$finded[] = $module;
				endforeach;
			endforeach;

			//cache 1 hour
			\Cache::set('exist_modules', $retvals, 3600);

			return $retvals;
		}
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
		$module2load = \Inflector::remove_head_backslash($module);
		if ( ! $path = \Module::exists($module2load)) throw new \InvalidArgumentException('Module not found.');
		$path.= 'classes/controller';
		if ( ! is_dir($path)) return false;

		// generate controller class name
		return \Inflector::dir_to_ctrl($path);
	}
}
