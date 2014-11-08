<?php
namespace Locomo;
class Actionset
{
	public static $actions  = array();

	/**
	 * add_actionset_arr()
	 */
	public static function add_actionset(
		$module,
		$realm = null,
		$name = null,
		$arr = array()
	)
	{
		if( ! isset(static::$actions[$realm])):
			static::$actions[$realm] = array();
		endif;
		static::$actions[$realm][$name] = $arr;
	}

	/**
	 * get_actionset()
	 */
	public static function get_actionset($controller = null, $module = null, $obj = null)
	{
		if(is_null($controller)) return false;

		//staticがあったら返す。なければ生成。
		if(isset(static::$actions[$controller])) return static::$actions[$controller];

		//Module::load()
		if( ! \Module::loaded($module) && ! is_null($module)){
			if( ! \Module::load($module)) die("module doesn't exist");
		}

		//一つのモジュールについてすべてのアクションセットを取得する
		static::set_actionset($controller, $module, $obj);
		if( ! isset(static::$actions[$controller])) return false;

		return static::$actions[$controller] ? static::$actions[$controller] : false;
	}

	/**
	 * set_actionset()
	 */
	public static function set_actionset($controller = null, $module = null, $obj = null)
	{
		//check args - search actionset class
		if(is_null($controller)) return array();
		$s_controller = substr(ucfirst(\Inflector::denamespace($controller)), 11);
		$config = \Config::load(strtolower($s_controller).'.php');
		$config = $config ?: \Config::load($s_controller.'::'.strtolower($s_controller).'.php');
		if( ! isset($config['actionset_classes'])) return array();
		$classes = $config['actionset_classes'];
		if(! is_array($classes)) throw new \InvalidArgumentException('Given class were not array. Received: '.$classes);

		//primary key
		$obj = is_object($obj) ? $obj : (object) array() ;
		$id = method_exists($obj, 'get_pk') ? $obj->get_pk() : null ;

		$actions = array();
		foreach($classes as $realm => $class):
			//methods
			$methods = array_flip(get_class_methods($class));
			$methods = \Arr::filter_prefixed($methods, 'actionset_');
			$methods = array_flip($methods);

			foreach($methods as $method):
				$p_method = 'actionset_'.$method;
				static::$actions[$controller][$realm][$method] = $class::$p_method($controller, $module, $obj, $id);
			endforeach;
		endforeach;

		if( ! isset(static::$actions[$controller])) return false;

		//整形
		$overrides = array();
		foreach(static::$actions[$controller] as $realm_name => $actions):
			foreach($actions as $action_k => $action):
				//prepare override
				if(isset(static::$actions[$controller][$realm_name][$action_k]['overrides'])){
					$overrides = array_merge($overrides, static::$actions[$controller][$realm_name][$action_k]['overrides']);
				}
			endforeach;
			//orderを修正
			static::$actions[$controller][$realm_name] = \Arr::multisort(static::$actions[$controller][$realm_name], array('order' => SORT_ASC,));
		endforeach;

		//override
		foreach($overrides as $realm_name => $urls):
			static::$actions[$controller][$realm_name]['override_url'] = $urls;
		endforeach;

		return true;
	}

	/**
	 * generate_uris()
	 */
	public static function generate_uris($module, $controller, $action, $actions, $exceptions = array())
	{
		static $exists = array();

		$urls = array();
		//check $exceptions
		if(\Request::main()->controller == $controller && in_array(\Request::main()->action, $exceptions)):
			return $urls;
		endif;

		//check auth
		if( ! \Auth::instance()->has_access(array($module, '\\'.$controller, $action))){
			return $urls;
		}

		//$actions to uri
		foreach($actions as $action):
			$url  = static::action2uri(\Arr::get($action, 0, false));
			$str  = \Arr::get($action, 1, false);
			$attr = \Arr::get($action, 2, array());
			if(! $url || ! $str || in_array($url, $exists)) continue;
			$exists[] = $url;
			$urls[] = \Html::anchor($url, $str, $attr);
		endforeach;

		return $urls;
	}

	/**
	 * action2uri()
	 */
	public static function action2uri($str)
	{
		if( ! $str) return false;
		$parse_uris = explode('/',$str);
		if(count($parse_uris) <= 2) throw new \InvalidArgumentException('Given action seems not a valid. maybe module is not set. even if using non module controller, set "locomo/controller_class/action".');

		if(is_null($parse_uris[0])) unset($parse_uris[0]);
		$parse_uris[1] = str_replace('_', '/', substr(strtolower(\Inflector::denamespace($parse_uris[1])), 11));
		return join('/',$parse_uris) ;
	}

	/**
	 * generate_menu_html()
	 */
	public static function generate_menu_html($obj, $ul_attr = array())
	{
		if( ! $obj) return false;

		$arr = array();
		foreach($obj as $label => $v):
			if($anchors = \Arr::get($v, 'urls', array())){
				$arr = array_merge($arr, $anchors);
			}
		endforeach;
		if( ! $arr) return false;

		//override
		if(isset($obj['override_url'])){
			$arr = $obj['override_url'];
		}
		return \Html::ul($arr, $ul_attr);
	}

	/**
	 * generate_bulk_anchors()
	 */
	public static function generate_bulk_anchors($module, $controller, $model, $opt, $nicename, $urls)
	{

//モジュールがない場合を想定する
		if(! $urls) return array();
		$target = join('/',array_slice(\Uri::segments(), 0, 4));
		$patterns[] = "{$module}/{$controller}/index_revision/{$model}";
		$patterns[] = "{$module}/{$controller}/each_index_revision/{$model}";
		$patterns[] = "{$module}/{$controller}/view_revision/{$model}";

		$target_short = join('/',array_slice(\Uri::segments(), 0, 3));
		$patterns_short[] = "{$module}/{$controller}/{$model}";
		if(in_array($target, $patterns) || in_array($target_short, $patterns_short)):
			$override_urls['base'] = array(
				\Html::anchor("{$module}/{$controller}/{$model}/?create=1","{$nicename}新規作成"),
				\Html::anchor("{$module}/{$controller}/index_revision/{$model}?opt={$opt}","設定履歴"),
			);
		endif;
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
