<?php
namespace Locomo;
class Inflector extends \Fuel\Core\Inflector
{
	/**
	 * ctrl_to_dir()
	 * class name to dir path
	 * @param string \Mod\Controller_Foo_Bar
	 * @return string "path/to/class"
	 */
	public static function ctrl_to_dir($controller = null)
	{
		if( ! $controller) throw new \InvalidArgumentException('argument must not be null or empty');
		return str_replace('_', '/', substr(strtolower(\Inflector::denamespace($controller)), 11));
	}

	/**
	 * dir_to_ctrl()
	 * dir path to controller class name
	 * @param string  "path/to/files"
	 * @return array array("\Controller_Foo_Bar" => "path/to/file")
	 */
	public static function dir_to_ctrl($path = null)
	{
		$cache_str = 'inflector_dir_to_ctrl_'.static::friendly_title($path);
		try
		{
			return \Cache::get($cache_str);
		}
		catch (\CacheNotFoundException $e)
		{
			// read files
			$paths = \File::read_dir($path);
			$paths = \Arr::flatten(\Arr::flatten($paths, '/'));
	
			// classify
			$classes = array();
			foreach ($paths as $k => $v)
			{
				$k = substr($k, 0, strrpos($k, '/'));
				$k.= \Str::ends_with($k, '_') ? '' : '/';
				$full_path = str_replace(DS.DS, DS, $path.DS.$k.$v);
				$class = static::path_to_ctrl($full_path);
				$classes[$class] = $full_path;
			}

			//cache 20 min
			\Cache::set($cache_str, $classes, 3600 / 3);

			return $classes;
		}
	}

	/**
	 * path_to_ctrl()
	 * path to controller class name
	 * @param string  "path/to/file"
	 * @return string "\Controller_Foo_Bar"
	 */
	public static function path_to_ctrl($path = null)
	{
		if( ! file_exists($path)) throw new \InvalidArgumentException('file not found.');
		$paths = explode('/', $path);

		// module name is next to modules dir.
		$mod_pos = \Arr::search($paths, 'modules');
		$module = $mod_pos ? \Arr::next_by_key($paths, $mod_pos, true) : null ;
		$module = $module ? '\\'.ucfirst($module).'\\' : '\\' ;

		// search controller position
		$class_pos = \Arr::search($paths, 'controller');
		if( ! $class_pos) throw new \InvalidArgumentException('controller not found.');

		//classify
		$class = ucfirst(join('/', array_slice($paths, $class_pos)));
		$class = \Inflector::words_to_upper(str_replace('/', '_', $module.$class));
		return str_replace('.php', '', $class);
	}
}
