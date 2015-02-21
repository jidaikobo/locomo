<?php
namespace Locomo;
class Actionset
{
	public static $actions     = array();
	public static $mod_actions = array();
	public static $disabled    = array();

	/**
	 * disabled()
	 * @param array $args
	 */
	public static function disabled($args)
	{
		// add disabled
		$controller = \Inflector::add_head_backslash(\Request::main()->controller);
		foreach ($args as $realm => $action)
		{
			static::$disabled[$realm] = \Arr::get(static::$disabled, $realm, array());
			\Arr::insert(static::$disabled[$realm], $action, 0);
		}
	}

	/**
	 * get_actionset()
	 * @param string $controller controller full class name
	 * @param object $obj use for auth. Fuel\Model object
	 * @param mixed $default
	 * @return array()
	 */
	public static function get_actionset($controller, $obj = null, $default = false)
	{
		// set actionset
		$controller = \Inflector::add_head_backslash($controller);
		$actionsets = static::set_actionset($controller, $obj) ? static::$actions[$controller] : $default;

		// check disabled
		foreach (static::$disabled as $realm => $disables)
		{
			foreach ($disables as $v)
			{
				\Arr::delete($actionsets[$realm], $v);
			}
		}

		// set base realm first
		$base = \Arr::get($actionsets, 'base');
		$retvals = array();
		if ($base) \Arr::set($retvals, 'base', $base);
		foreach ($actionsets as $realm => $v)
		{
			if ($realm == 'base') continue;
			\Arr::set($retvals, $realm, $v);
		}

		return $retvals;
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

		// paths
		$paths = array(
			APPPATH.'classes',
			LOCOMOPATH.'classes',
		);

		// search actionset - remove 'Controller_'
		$name = strtolower(substr(\Inflector::denamespace($controller), 11));
		$module = \Inflector::get_modulename($controller, $default = '');
		if ($module)
		{
			\Module::loaded($module) or \Module::load($module);
			$paths = array(
				APPPATH.'classes',
				APPPATH.'modules/'.$module.'/classes',
				LOCOMOPATH.'classes',
				LOCOMOPATH.'modules/'.$module.'/classes',
			);
		}
		$finder = \Finder::forge($paths);
		$actionset = $finder->locate('actionset', str_replace('_', DS, $name));
		if ( ! $actionset) return;

		// actionset class
		$class = str_replace('Controller_', 'Actionset_', $controller);
		if ( ! class_exists($class)) return;

		// cli cannot use Request
		if ( ! \Request::main()) return false;

		// primary key
		$obj = is_object($obj) ? $obj : (object) array() ;
		$id = method_exists($obj, 'get_pk') ? $obj->get_pk() : null ;

		// get controllers actions - search prefixed 'action_'
		$act_methods = array_flip(get_class_methods($controller));
		$act_methods = \Arr::filter_prefixed($act_methods, 'action_');

		// methods - search prefixed 'actionset_'
		$methods = array_flip(get_class_methods($class));
		$methods = \Arr::filter_prefixed($methods, 'actionset_');

		foreach($methods as $method => $v)
		{
			// eliminate non exists action
//			if ( ! array_key_exists($method, $act_methods)) continue;

			$p_method = 'actionset_'.$method;
			$as = $class::$p_method($controller, $obj, $id);
			// require "urls" or "dependencies"
			if (\Arr::get($as, 'urls.0') || \Arr::get($as, 'dependencies.0'))
			{
				$realm = \Arr::get($as, 'realm', 'base');
				$order = \Arr::get($as, 'order', 10);
				$as['order'] = $order;
				static::$actions[$controller][$realm][$method] = $as;
			}
		}
		if (empty(static::$actions[$controller])) return false;

		// order
		foreach (static::$actions[$controller] as $realm => $v)
		{
			static::$actions[$controller][$realm] = \Arr::multisort(
				static::$actions[$controller][$realm],
				array('order' => SORT_ASC,)
			);
		}

		return true;
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
		 // orderが設定されてなければ10にする。
		\Arr::set($arr, 'order', \Arr::get($arr, 'order', 10));
		static::$actions[$controller][$realm]['added'] += $arr;
	}

	/**
	 * generate_urls()
	 */
	public static function generate_urls($locomo_path, $actions, $exceptions = array(), $realm = 'base')
	{
		static $exists = array();

		list($controller, $action) = explode('::', $locomo_path);

		// check $exceptions
		$urls = array();
		$current_controller = \Inflector::add_head_backslash(\Request::main()->controller);
		if ($current_controller == $controller && in_array(\Request::main()->action, $exceptions))
		{
			return $urls;
		}

		// check auth
		if ( ! \Auth::has_access($locomo_path))
		{
			return $urls;
		}

		// $actions to uri
		foreach($actions as $v)
		{
			$url  = \Inflector::ctrl_to_dir(\Arr::get($v, 0, false));
			$str  = \Arr::get($v, 1, false);
			$attr = \Arr::get($v, 2, array());
//			if (! $url || ! $str || in_array($url, \Arr::get($exists, $realm, array()))) continue;
//			$exists[$realm][] = $url;
			if (! $url || ! $str) continue;
			$urls[] = \Html::anchor($url, $str, $attr);
		}
		$urls = array_unique($urls);

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
			'acl_exp'      => \Util::get_locomo($controller, 'nicename').'の管理権限です。すべての行為が許されます。',
			'order'        => 0,
		);
		return $retvals;
	}
}
