<?php
namespace Locomo;
class Inflector extends \Fuel\Core\Inflector
{
	/**
	 * ctrl_to_dir()
	 * class name to dir path
	 * @param string \Mod\Controller_Foo_Bar[/action]
	 * @return string "path/to/class[/action]"
	 */
	public static function ctrl_to_dir($controller = null, $delete_locomo = true)
	{
		if ( ! $controller) throw new \InvalidArgumentException('argument must not be null or empty');

		$controller = static::add_head_backslash($controller);
		$controller = $delete_locomo ? str_replace('\Locomo', '', $controller) : $controller;

		// if T_PAAMAYIM_NEKUDOTAYIM exists
		$strs = explode('::action_', $controller);
		$strs[0] = str_replace('\\', '', $strs[0]);
		$strs[0] = str_replace(array('Controller_','_'), '/', $strs[0]);
		$strs[0] = strtolower($strs[0]);
		return join('/', $strs);
	}

	/**
	 * dir_to_ctrl()
	 * dir path to controller class name
	 * @param string  "path/to/files"
	 * @return array array("\Controller_Foo_Bar" => "path/to/file")
	 */
	public static function dir_to_ctrl($path = null)
	{
		// static cache
		static $cache = array();
		if (isset($cache[$path])) return $cache[$path];

		$cache_str = 'inflector_dir_to_ctrl_'.static::friendly_title($path);
		try
		{
			// root not use cache
			if (\Auth::is_root()) throw new \CacheNotFoundException();
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

			// static cache
			$cache[$path] = $classes;

			// cache 20 min
			\Cache::delete($cache_str);
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
		if ( ! file_exists($path)) throw new \InvalidArgumentException('file not found.');
		$paths = explode('/', $path);

		// module name is next to modules dir.
		$mod_pos = \Arr::search($paths, 'modules');
		$module = $mod_pos ? \Arr::next_by_key($paths, $mod_pos, true) : null ;
		$module = $module ? '\\'.ucfirst($module).'\\' : '\\' ;

		// search controller position
		$class_pos = \Arr::search($paths, 'controller');
		//if ( ! $class_pos) throw new \InvalidArgumentException('controller not found.');

		// classify
		$class = join('/', array_slice($paths, $class_pos));
		$class = \Inflector::words_to_upper(str_replace(LOCOMOPATH.'classes/', '', $class));
		$class = \Inflector::words_to_upper(str_replace('/', '_', $module.$class));
		return str_replace('.php', '', $class);
	}

	/**
	 * get_modulename()
	 * @param string [\MODNAME\]Controller_CTRL
	 * @return string [MODNAME|false]
	 */
	public static function get_modulename($str = null, $default = false)
	{
		$module = static::get_namespace($str) ?: false;
		$module = strtolower(substr($module, 0, -1));
		if( ! $module) return false;
		return \Module::exists($module) ? $module : $default;
	}

	/**
	 * ctrl_to_safestr()
	 * @param string [\MODNAME\]Controller_CTRL
	 * @return string [-MODNAME-]Controller_CTRL
	 */
	public static function ctrl_to_safestr($str = null)
	{
		return str_replace('\\', '-', $str);
	}

	/**
	 * safestr_to_ctrl()
	 * @param string [\MODNAME\]Controller_CTRL
	 * @return string [MODNAME|false]
	 */
	public static function safestr_to_ctrl($str = null)
	{
		return static::words_to_upper(str_replace('-', '\\', $str));
	}

	/**
	 * maybe_ctrl_to_model()
	 * @param string [\MODNAME\]Controller_CTRL
	 * @return string [\MODNAME\]Model_CTRL
	 */
	public static function maybe_ctrl_to_model($ctrl)
	{
		return str_replace('Controller', 'Model', $ctrl);
	}


	/**
	 * get_root_relative_path()
	 * @param string http://exmple.com/root_dir/ctrl/action
	 * @return string /root_dir/ctrl/action
	 */
	public static function get_root_relative_path($url = '')
	{
		$http_host = \Input::server('HTTP_HOST');
		$pos = strpos(\Uri::base(false), $http_host) + strlen($http_host);
		return substr($url, $pos);
	}

	/**
	 * remove_head_backslash()
	 * @param string [\\]str
	 * @return string str
	 */
	public static function remove_head_backslash($str = null)
	{
		return trim($str, '\\');
	}

	/**
	 * add_head_backslash()
	 * @param string [\\]str
	 * @return string \\str
	 */
	public static function add_head_backslash($str = null)
	{
		return '\\'.self::remove_head_backslash($str);
	}
}
