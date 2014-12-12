<?php
namespace Locomo;
class Actionset
{
	public static $actions  = array();

	/**
	 * add_actionset()
	 * use at controllers' business logic section
	 * see sample at \Revision\Traits_Controller_Revision::action_each_index_revision
	 *
	 * @param string $main_controller	\NMSPC\Controller_NAME
	 * @param string $realm	[base|option|index|ctrl|...]
	 * @param arr    $arr	array(array(\NMSPC\Controller_NAME.DS.'ACTION', MENUSTR, ATTR))
	 */
	public static function add_actionset($main_controller, $realm = null, $arr = array())
	{
		if (is_null($realm)) throw new \InvalidArgumentException('realm is missing.');
		$main_controller = \Inflector::remove_head_backslash($main_controller);
		if ( ! isset(static::$actions[$main_controller][$main_controller]['actionset'][$realm]))
		{
			static::$actions[$main_controller][$main_controller]['actionset'][$realm] = array();
		}
		static::$actions[$main_controller][$main_controller]['actionset'][$realm]['added'] = $arr;
	}

	/**
	 * get_actionset()
	 * @param string $ctrl_or_mod controller full class name or module dir name
	 * @param object $obj use for auth. Fuel\Model object
	 * @return array()
	 */
	public static function get_actionset($ctrl_or_mod = null, $obj = null)
	{
		if (is_null($ctrl_or_mod)) throw new \InvalidArgumentException('Argument must be controller name or module name.');

		// judge module or controller
		$module = strpos($ctrl_or_mod, 'Controller_') !== false ? null : \Inflector::add_head_backslash($ctrl_or_mod);
		$controller = $module ? null : $ctrl_or_mod;

		// Module::load() to read config
		$module2load = \Inflector::remove_head_backslash($module);
		if ( ! \Module::loaded($module2load) && ! is_null($module))
		{
			if ( ! \Module::load($module2load)) throw new \InvalidArgumentException('module doesn\'t exist');
		}

		// set actionset
		$main_controller = static::set_actionset($controller, $module, $obj);

		return $main_controller ? static::$actions[$main_controller] : false;
	}

	/**
	 * set_actionset()
	 * @param string $controller controller full class name
	 * @param string $module module dir name
	 * @param object $obj use for auth. Fuel\Model object
	 * @return [bool|string main_controller]
	 */
	public static function set_actionset($main_controller = null, $module = null, $obj = null)
	{
		if (is_null($main_controller) && is_null($module)) return false;

		// check args - if module, search contain controller
		$controllers = array();
		$classes = $module ? array_keys(\Module::get_controllers($module)) : array($main_controller);
		foreach($classes as $class)
		{
			$class = \Inflector::add_head_backslash($class);
			if ( ! property_exists($class, 'locomo')) continue;

			$actionset = \Arr::get($class::$locomo, 'actionset');
			$actionset_class = \Arr::get($class::$locomo, 'actionset_classes');

			if ( ! $actionset && ! $actionset_class) continue;
			$controllers[$class] = $class::$locomo;
		}

		// primary key
		$obj = is_object($obj) ? $obj : (object) array() ;
		$id = method_exists($obj, 'get_pk') ? $obj->get_pk() : null ;

		// remove head backslash when it has no necessity
		$main_controller = \Inflector::remove_head_backslash($main_controller);

		$actions = array();
		// controllers
		foreach($controllers as $k => $p)
		{
			// actionset_classes
			foreach(\Arr::get($p, 'actionset_classes', array()) as $realm => $class)
			{
				// methods - search prefixed 'actionset_'
				$methods = get_class_methods($class);
				if (! is_array($methods)) continue;
				$methods = array_flip($methods);
				$methods = \Arr::filter_prefixed($methods, 'actionset_');
				$methods = array_flip($methods);

				foreach($methods as $method)
				{
					$p_method = 'actionset_'.$method;
					$as = $class::$p_method($k, $obj, $id);
					// not exists "urls" and "dependencies", retun nothing
					if (! \Arr::get($as, 'urls.0') && ! \Arr::get($as, 'dependencies.0')) continue;

					$k = \Inflector::remove_head_backslash($k);
					static::$actions[$main_controller][$k]['nicename'] = $p['nicename'];
					static::$actions[$main_controller][$k]['actionset'][$realm][$method] = $as;
				}
			}

			// actionset not by actionset class
			foreach(\Arr::get($p, 'actionset', array()) as $realm => $vv)
			{
				foreach ($vv as $kk => $vvv)
				{
					$links = array();
					foreach ($vvv["urls"] as $uri => $str)
					{
						$links[] = \Html::anchor(\Uri::create(\Inflector::ctrl_to_dir($uri)), $str);
					}
					$vv[$kk]['urls'] = $links;
				}
				$ctrl = \Inflector::remove_head_backslash($k);
				static::$actions[$main_controller][$ctrl]['nicename'] = $p['nicename'];
				static::$actions[$main_controller][$ctrl]['actionset'][$realm] = $vv;
			}
		}
		if ( ! isset(static::$actions[$main_controller])) return false;

		// tidy up
		$overrides = array();
		foreach (static::$actions[$main_controller] as $k => $v)
		{
			foreach ($v["actionset"] as $realm => $vv)
			{
				foreach ($vv as $kkk => $vvv)
				{
						// prepare override
						if (isset($vvv['overrides']))
						{
							$overrides = array_merge($overrides, $vvv['overrides']);
						}
					}
			}

			// order
			static::$actions[$main_controller][$k]['actionset'][$realm] = \Arr::multisort(static::$actions[$main_controller][$k]['actionset'][$realm], array('order' => SORT_ASC,));
		}

		// override
		foreach($overrides as $realm_name => $urls)
		{
			static::$actions[$main_controller][$k]['actionset'][$realm_name]['override_url'] = $urls;
		}

		return $main_controller;
	}

	/**
	 * generate_urls()
	 */
	public static function generate_urls($controller, $action, $actions, $exceptions = array(), $realm = 'base')
	{
		static $exists = array();

		$urls = array();
		// check $exceptions
		if (\Request::main()->controller == $controller && in_array(\Request::main()->action, $exceptions))
		{
			return $urls;
		}

		// check auth
		if ( ! \Auth::instance()->has_access(array($controller, $action)))
		{
			return $urls;
		}

		// $actions to uri
		foreach($actions as $v)
		{
			$url  = \Inflector::ctrl_to_dir(\Arr::get($v, 0, false));
			$str  = \Arr::get($v, 1, false);
			$attr = \Arr::get($v, 2, array());
			if (! $url || ! $str || in_array($url, \Arr::get($exists, $realm, array()))) continue;
			$exists[$realm][] = $url;
			$urls[] = \Html::anchor($url, $str, $attr);
		}

		return $urls;
	}

	/**
	 * generate_menu_html()
	 */
	public static function generate_menu_html($obj, $ul_attr = array())
	{
		if ( ! $obj) return false;

		$arr = array();
		foreach($obj as $label => $v)
		{
			if ($anchors = \Arr::get($v, 'urls', array()))
			{
				$arr = array_merge($arr, $anchors);
			}
		}
		if ( ! $arr) return false;

		// override
		if (isset($obj['override_url']))
		{
			$arr = $obj['override_url'];
		}

		return \Html::ul($arr, $ul_attr);
	}

	/**
	 * remove_actionset($realm)
	 */
	public static function remove_actionset($controller, $realm)
	{
		unset(static::$actions[$controller][$realm]);
	}
}
