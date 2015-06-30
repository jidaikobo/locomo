<?php
namespace Locomo;
class Actionset
{
	public static $actions           = array();
	public static $mod_actions       = array();
	public static $disabled          = array();

	/**
	 * disabled()
	 * @param array $args array('\Controller_FOO/bar',...)
	 */
	public static function disabled($args)
	{
		// add disabled
		$args = array_map(array('Inflector', 'ctrl_to_dir'), $args);
		static::$disabled = array_merge(static::$disabled, $args);
	}

	/**
	 * get_actionset()
	 * @param string $controller controller full class name
	 * @param object $obj use for auth. Fuel\Model object
	 * @param mixed $default
	 * @return array()
	 */
	public static function get_actionset($controller, $obj = null, $default = array())
	{
		// set actionset
		$controller = \Inflector::add_head_backslash($controller);
		$actionsets = static::set_actionset($controller, $obj) ? static::$actions[$controller] : $default;

		// check disabled
		foreach ($actionsets as $action => $actionset)
		{
			if ( ! isset($actionset['urls']) || empty($actionset['urls'])) continue;
			foreach (static::$disabled as $disabled_action)
			{
				foreach ($actionset['urls'] as $k => $v)
				{
					if (strpos($v, $disabled_action)) unset($actionsets[$action]['urls'][$k]);
				}
			}
		}

		// update
		static::$actions[$controller] = $actionsets;
		return $actionsets;
	}

	/**
	 * get_actionset_by_realm()
	 * @param string $controller controller full class name
	 * @param array realm name
	 * @param bool $exclusive
	 * exclusiveで条件を反転
	 * @return array()
	 */
	public static function get_actionset_by_realm($controller, $realms = array(), $exclusive = false)
	{
		// check existence
		$controller = \Inflector::add_head_backslash($controller);
		if ( ! isset(static::$actions[$controller])) throw new \Exception("this method must be called after \\Actionset::get_actionset()");

		// retvals
		$retvals = array() ;
		if ($exclusive)
		{
			foreach (static::$actions[$controller] as $ctrl => $action)
			{
				$retvals[$action['realm']][$ctrl] = $action;
			}
		}

		// generate
		foreach (static::$actions[$controller] as $ctrl => $action)
		{
			$realm = \Arr::get($action, 'realm');
			if ($exclusive)
			{
				if (in_array($realm, $realms))
				{
					unset($retvals[$realm]);
				}
			} elseif (in_array($realm, $realms)) {
				$retvals[$realm][$ctrl] = $action;
			}
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
		$pk = method_exists($obj, 'primary_key') ? $obj::primary_key()[0] : null;
		$id = $pk ? $obj->$pk : null ;

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
				$realm       = \Arr::get($as, 'realm', 'base');
				$as['realm'] = $realm;
				$as['order'] = \Arr::get($as, 'order', 10);

				// auth check
				foreach (\Arr::get($as, 'urls', array()) as $kk => $vv)
				{
					// \Controller/action/path/to/foo -> \Controller/action
					$locomopath = join('/', array_slice(explode('/', $vv[0]), 0, 2));

					// \Controller/action?foo=bar -> \Controller/action
					$locomopath = strpos($locomopath, '?') !== false ? substr($locomopath, 0, strpos($locomopath, '?')) : $locomopath ;

					if ( ! \Auth::has_access($locomopath))
					{
						unset($as['urls'][$kk]);
					} else {
						// generate url
						$url  = \Inflector::ctrl_to_dir(\Arr::get($vv, 0, false));
						$str  = \Arr::get($vv, 1, false);
						$attr = \Arr::get($vv, 2, array());
						if (! $url || ! $str) unset($as['urls'][$kk]);
						$as['urls'][$kk] = \Html::anchor($url, $str, $attr);
					}
				}
				if (isset($as['urls'])) $as['urls'] = array_unique($as['urls']);
				static::$actions[$controller][$method] = $as;
			}
		}
		if (empty(static::$actions[$controller])) return false;

		// order
		foreach (static::$actions[$controller] as $realm => $v)
		{
			static::$actions[$controller] = \Arr::multisort(
				static::$actions[$controller],
				array('realm' => SORT_ASC, 'order' => SORT_ASC,)
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
		$key = md5(serialize($arr));

		if ( ! isset(static::$actions[$controller]))
		{
			static::$actions[$controller][$key] = array();
		}

		// set order and realm
		\Arr::set($arr, 'order', \Arr::get($arr, 'order', 10));
		\Arr::set($arr, 'realm', $realm);

		// set value
		static::$actions[$controller][$key] = $arr;
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
		$arr = array_unique($arr);

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
			'explanation'  => \Util::get_locomo($controller, 'nicename').'の管理権限です。',
			'order'        => 0,
		);
		return $retvals;
	}
}
