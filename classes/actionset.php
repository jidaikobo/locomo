<?php
namespace Locomo;
class Actionset
{
	public static $actions     = array();
	public static $mod_actions = array();

	/**
	 * get_actionset()
	 * @param string $controller controller full class name
	 * @param object $obj use for auth. Fuel\Model object
	 * @param mixed $default
	 * @return array()
	 */
	public static function get_actionset($controller, $obj = null, $default = false)
	{
		$controller = \Inflector::add_head_backslash($controller);
// do not turn below on. add_actionset() generate static member variable first
//		if ( ! empty(static::$actions[$controller])) return static::$actions[$controller];
		return static::set_actionset($controller, $obj) ? static::$actions[$controller] : $default;
	}

	/**
	 * get_module_actionset()
	 * @param string $module module name
	 * @param mixed $default
	 * @return array()
	 */
	public static function get_module_actionset($module, $obj = null, $default = false)
	{
		if ( ! empty(static::$mod_actions[$module])) return static::$mod_actions[$module];
		\Module::loaded($module) or \Module::load($module);
		foreach (\Module::get_controllers($module) as $ctrl => $v)
		{
			if( ! $actionset = static::get_actionset($ctrl, $obj)) continue;
			static::$mod_actions[$module][$ctrl] = $actionset;
		}
		return \Arr::get(static::$mod_actions, $module) ?: $default;
	}

	/**
	 * set_actionset()
	 * @param string $controller controller full class name
	 * @param object $obj use for auth. Fuel\Model object
	 * @return [bool|string main_controller]
	 */
	public static function set_actionset($controller, $obj = null)
	{
		// controller
		$controller = \Inflector::add_head_backslash($controller);

		// search actionset - remove 'Controller_'
		$name = strtolower(substr(\Inflector::denamespace($controller), 11));
		$module = \Inflector::get_modulename($controller, $default = '');
		if ($module) \Module::loaded($module) or \Module::load($module);
		$paths = array(
			APPPATH.'classes',
			APPPATH.'modules/'.$module.'/classes',
			LOCOMOPATH.'classes',
			LOCOMOPATH.'modules/'.$module.'/classes',
		);
		$finder = \Finder::forge($paths);
		$actionset = $finder->locate('actionset', $name);
		if ( ! $actionset) return;


		// actionset class
		$class = str_replace('Controller_', 'Actionset_', $controller);
		if ( ! class_exists($class)) return;

		// primary key
		$obj = is_object($obj) ? $obj : (object) array() ;
		$id = method_exists($obj, 'get_pk') ? $obj->get_pk() : null ;

		// get controllers actions - search prefixed 'action_'
		$act_methods = get_class_methods($controller);
		$act_methods = array_flip($act_methods);
		$act_methods = \Arr::filter_prefixed($act_methods, 'action_');
		$act_methods = array_flip($act_methods);

		// methods - search prefixed 'actionset_'
		$methods = get_class_methods($class);
		if (! is_array($methods)) return;
		$methods = array_flip($methods);
		$methods = \Arr::filter_prefixed($methods, 'actionset_');
		$methods = array_flip($methods);

		foreach($methods as $method)
		{
			// eliminate non exists action
			if ( ! in_array($method, $act_methods)) continue;

			$p_method = 'actionset_'.$method;
			$as = $class::$p_method($controller, $obj, $id);
			// require "urls" or "dependencies"
			if (\Arr::get($as, 'urls.0') || \Arr::get($as, 'dependencies.0'))
			{
				$realm = \Arr::get($as, 'realm', 'base');
				static::$actions[$controller][$realm][$method] = $as;
			}
		}
		if (empty(static::$actions[$controller])) return false;

		// override and order
		$overrides = array();
		foreach (static::$actions[$controller] as $realm => $v)
		{
			foreach ($v as $kk => $vv)
			{
				if (isset($vv['overrides']))
				{
					$overrides = array_merge($overrides, $vv['overrides']);
				}
			}

			// order
			static::$actions[$controller][$realm] = \Arr::multisort(
				static::$actions[$controller][$realm],
				array('order' => SORT_ASC,)
			);
		}

		// override
		foreach($overrides as $realm => $urls)
		{
			static::$actions[$controller][$realm]['override_url'] = $urls;
		}

		return true;
/*
		if ( ! property_exists($controller, 'locomo')) return false;
		$locomo = $controller::$locomo;

		// primary key
		$obj = is_object($obj) ? $obj : (object) array() ;
		$id = method_exists($obj, 'get_pk') ? $obj->get_pk() : null ;

		// get controllers actions
		$act_methods = get_class_methods($controller);
		$act_methods = array_flip($act_methods);
		$act_methods = \Arr::filter_prefixed($act_methods, 'action_');
		$act_methods = array_flip($act_methods);

		// actionset by actionset classes
		$actions = array();
		foreach(\Arr::get($locomo, 'actionset_classes', array()) as $realm => $class)
		{
			// methods - search prefixed 'actionset_'
			$methods = get_class_methods($class);
			if (! is_array($methods)) continue;
			$methods = array_flip($methods);
			$methods = \Arr::filter_prefixed($methods, 'actionset_');
			$methods = array_flip($methods);

			foreach($methods as $method)
			{
				// eliminate non exists action
				if ( ! in_array($method, $act_methods)) continue;

				$p_method = 'actionset_'.$method;
				$as = $class::$p_method($controller, $obj, $id);
				// require "urls" or "dependencies"
				if (\Arr::get($as, 'urls.0') || \Arr::get($as, 'dependencies.0'))
				{
					static::$actions[$controller][$realm][$method] = $as;
				}
			}
		}

		// actionset by actionset methods
		foreach(\Arr::get($locomo, 'actionset_methods', array()) as $realm => $methods)
		{
			$as = array();
			foreach($methods as $method)
			{
				$k_method = substr($method, strlen('actionset_'));
				$as[$k_method] = $controller::$method($controller, $obj, $id);
			}
			static::$actions[$controller][$realm] = $as;
		}

		// actionset by actionset array
		foreach(\Arr::get($locomo, 'actionset', array()) as $realm => $v)
		{
			foreach ($v as $k => $vv)
			{
				$links = array();
				foreach ($vv["urls"] as $uri => $str)
				{
					$links[] = \Html::anchor(\Uri::create(\Inflector::ctrl_to_dir($uri)), $str);
				}
				$v[$k]['urls'] = $links;
			}
			static::$actions[$controller][$realm] = $v;
		}

		if (empty(static::$actions[$controller])) return false;

		// override and order
		$overrides = array();
		foreach (static::$actions[$controller] as $realm => $v)
		{
			foreach ($v as $kk => $vv)
			{
				if (isset($vv['overrides']))
				{
					$overrides = array_merge($overrides, $vv['overrides']);
				}
			}

			// order
			static::$actions[$controller][$realm] = \Arr::multisort(
				static::$actions[$controller][$realm],
				array('order' => SORT_ASC,)
			);
		}

		// override
		foreach($overrides as $realm => $urls)
		{
			static::$actions[$controller][$realm]['override_url'] = $urls;
		}

		return true;
*/
	}

	/**
	 * add_actionset()
	 * use at controllers' business logic section
	 * see sample at \Controller_Traits_Revision::action_each_index_revision
	 *
	 * @param string $controller controller full class name
	 * @param string $realm	[base|option|index|ctrl|...]
	 * @param arr    $arr	array(array([\NMSPC]\Controller_NAME.DS.'ACTION', MENUSTR, ATTR))
	 */
	public static function add_actionset($controller, $realm, $arr = array())
	{
		$controller = \Inflector::add_head_backslash($controller);
		if ( ! isset(static::$actions[$controller][$realm]))
		{
			static::$actions[$controller][$realm]['added'] = array();
		}
		static::$actions[$controller][$realm]['added'] += $arr;
	}

	/**
	 * generate_urls()
	 */
	public static function generate_urls($locomo_path, $actions, $exceptions = array(), $realm = 'base')
	{
		static $exists = array();

		list($controller, $action) = explode('/', $locomo_path);

		$urls = array();
		// check $exceptions
		if (\Request::main()->controller == $controller && in_array(\Request::main()->action, $exceptions))
		{
			return $urls;
		}

		// check auth
		if ( ! \Auth::instance()->has_access($locomo_path))
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
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '管理権限',
			'show_at_top'  => false,
			'acl_exp'      => $controller::$locomo['nicename'].'の管理権限です。すべての行為が許されます。',
			'order'        => 0,
		);
		return $retvals;
	}
}
