<?php
namespace Locomo;
class Actionset
{
	public static $actions  = array();

	/**
	 * set_unique_key()
	 */
	private static function set_unique_key($str = '')
	{
		return md5($str);
	}

	/**
	 * add_actionset()
	 * use at controllers' business logic section
	 * see sample at \Revision\Traits_Controller_Revision::action_each_index_revision
	 *
	 * @param string $controller	\NMSPC\Controller_NAME
	 * @param string $realm	[base|option|index]
	 * @param arr    $arr	array(array($module.DS.\NMSPC\Controller_NAME.DS.'ACTION', MENUSTR, ATTR))
	 */
	public static function add_actionset($controller, $realm = null, $arr = array())
	{
		$controller = \Inflector::remove_head_backslash($controller);
		$unique_key = static::set_unique_key($controller);
		if ( ! isset(static::$actions[$unique_key][$controller]['actionset'][$realm]))
		{
			static::$actions[$unique_key][$controller]['actionset'][$realm] = array();
		}
		static::$actions[$unique_key][$controller]['actionset'][$realm]['added'] = $arr;
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
		$unique_key = static::set_actionset($controller, $module, $obj);

		return $unique_key ? static::$actions[$unique_key] : false;
	}

	/**
	 * set_actionset()
	 * @param string $controller controller full class name
	 * @param string $module module dir name
	 * @param object $obj use for auth. Fuel\Model object
	 * @return [bool|string unique_key]
	 */
	public static function set_actionset($controller = null, $module = null, $obj = null)
	{
		if (is_null($controller) && is_null($module)) return false;

		// check args - if module, search contain controller
		$controllers = array();
		$classes = $module ? array_keys(\Module::get_controllers($module)) : array($controller);
		foreach($classes as $class)
		{
			$class = \Inflector::add_head_backslash($class);
			if ( ! property_exists($class, 'locomo')) continue;
			if ( ! \Arr::get($class::$locomo, 'actionset_classes')) continue;
			$controllers[$class] = $class::$locomo;
		}

		// primary key
		$obj = is_object($obj) ? $obj : (object) array() ;
		$id = method_exists($obj, 'get_pk') ? $obj->get_pk() : null ;


		// remove head backslash when it has no necessity
		$controller = \Inflector::remove_head_backslash($controller);
		// unique_key
//		$unique_key = $module ?: $controller;
		$unique_key = static::set_unique_key($controller);

		$actions = array();
		//controllers
		foreach($controllers as $k => $p)
		{
			// actionset_classes
			foreach($p['actionset_classes'] as $realm => $class)
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
					static::$actions[$unique_key][$k]['nicename'] = $p['nicename'];
					static::$actions[$unique_key][$k]['actionset'][$realm][$method] = $as;
				}
			}
		}

		if ( ! isset(static::$actions[$unique_key])) return false;

		// tidy up
		$overrides = array();
		foreach (static::$actions[$unique_key] as $k => $v)
		{
			foreach ($v["actionset"] as $realm => $vv)
			{
					// prepare override
					if (isset($vv['overrides']))
					{
						$overrides = array_merge($overrides, $vv['overrides']);
					}
			}

			// order
			static::$actions[$unique_key][$k]['actionset'][$realm] = \Arr::multisort(static::$actions[$unique_key][$k]['actionset'][$realm], array('order' => SORT_ASC,));
		}

		// override
		foreach($overrides as $realm_name => $urls)
		{
			static::$actions[$unique_key][$controller]['actionset'][$realm_name]['override_url'] = $urls;
		}

		return $unique_key;
	}

	/**
	 * generate_uris()
	 */
	public static function generate_uris($controller, $action, $actions, $exceptions = array())
	{
		static $exists = array();

		$urls = array();
		//check $exceptions
		if (\Request::main()->controller == $controller && in_array(\Request::main()->action, $exceptions))
		{
			return $urls;
		}

		//check auth
		if ( ! \Auth::instance()->has_access(array($controller, $action)))
		{
			return $urls;
		}

		//$actions to uri
		foreach($actions as $v)
		{
			$url  = \Inflector::ctrl_to_dir(\Arr::get($v, 0, false));
			$str  = \Arr::get($v, 1, false);
			$attr = \Arr::get($v, 2, array());
			if (! $url || ! $str || in_array($url, $exists)) continue;
			$exists[] = $url;
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

		//override
		if (isset($obj['override_url']))
		{
			$arr = $obj['override_url'];
		}

		return \Html::ul($arr, $ul_attr);
	}

	/**
	 * generate_bulk_anchors()
	 */
	public static function generate_bulk_anchors($module, $controller, $model, $opt, $nicename, $urls)
	{
		if (! $urls) return array();
		//target and patterns
		$target = join('/',array_slice(\Uri::segments(), 0, 4));
		$patterns[] = "{$module}/{$controller}/index_revision/{$model}";
		$patterns[] = "{$module}/{$controller}/each_index_revision/{$model}";
		$patterns[] = "{$module}/{$controller}/view_revision/{$model}";

		//target_short and patterns_short
		$target_short = join('/',array_slice(\Uri::segments(), 0, 3));
		$patterns_short[] = "{$module}/{$controller}/{$model}";

		//target_more_short and patterns_more_short
		$target_more_short = join('/',array_slice(\Uri::segments(), 0, 2));
		$patterns_more_short[] = "{$module}/{$model}";
		$patterns_more_short[] = "{$controller}/{$model}";

		//override url
		if
		(
			in_array($target, $patterns) ||
			in_array($target_short, $patterns_short) ||
			in_array($target_more_short, $patterns_more_short)
		)
		{
			$override_urls['base'] = array(
				\Html::anchor("{$module}/{$controller}/{$model}/?create=1","{$nicename}新規作成"),
				\Html::anchor("{$module}/{$controller}/index_revision/{$model}?opt={$opt}","設定履歴"),
			);
		}
		return @$override_urls ?: array();
	}

	/**
	 * remove_actionset($realm)
	 */
	public static function remove_actionset($controller, $realm)
	{
		unset(static::$actions[$controller][$realm]);
	}
}
