<?php
namespace Locomo;
class Util
{
	/**
	 * fetch_tpl()
	 */
	public static function fetch_tpl($path = null)
	{
		is_null($path) and \Response::redirect(\Uri::base());

		$tpl_path         = PKGPROJPATH.'modules/'.$path;
		$tpl_path_default = PKGCOREPATH.'modules/'.$path;

		if(file_exists($tpl_path)):
			return $tpl_path;
		elseif(file_exists($tpl_path_default)):
			return $tpl_path_default;
		else:
			return false;
		endif;
	}
	
	/**
	 * get_valid_controller_name()
	 */
	public static function get_valid_controller_name($controller = null)
	{
		is_null($controller) and \Response::redirect(\Uri::base());
		$controller_ucfirst = ucfirst($controller);
		return "\\$controller_ucfirst\Controller_".$controller_ucfirst;
	}

	/**
	 * get_valid_model_name()
	 */
	public static function get_valid_model_name($controller = null)
	{
		is_null($controller) and \Response::redirect(\Uri::base());
		$controller_ucfirst = ucfirst($controller);
		return "\\$controller_ucfirst\Model_".$controller_ucfirst;
	}

	/**
	 * get_all_configs()
	 */
	public static $configs = array();

	public static function get_all_configs()
	{
		if(static::$configs) return static::$configs;

		//modules' config
		foreach (\Config::get('module_paths') as $path):
			foreach (glob($path.'*') as $modname):
				if( ! is_dir($modname)) continue;
				$modname_str = basename($modname);
				static::$configs[$modname_str] = \Config::load($modname.'/config/'.$modname_str.'.php', true, true);
			endforeach;
		endforeach;

		//controllers
		foreach (glob(APPPATH.DS.'classes'.DS.'controller'.DS.'*') as $classname):
			$classname = basename($classname);
			static::$configs[$classname] = \Config::load($classname);
		endforeach;

		foreach(static::$configs as $k => $config):
			static::$configs[$k]['nicename'] = \Arr::get($config, 'nicename', false) ?: $k;
			static::$configs[$k]['index_nicename'] = \Arr::get($config, 'index_nicename', false) ?: $k;
			static::$configs[$k]['order_in_menu'] = \Arr::get($config, 'order_in_menu', false) ?: 200;
		endforeach;

		//array_multisort
		static::$configs = \Arr::multisort(static::$configs, array('order_in_menu' => SORT_ASC,));

		return static::$configs;
	}
}
