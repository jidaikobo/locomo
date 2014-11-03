<?php
namespace Locomo;
class Actionset
{
	public static $actions  = array();
	protected static $instance = null;
	protected static $modules = array();

	/**
	 * __construct()
	 */
	public function __construct($module)
	{
		//初回のみ対象モジュールのパスを格納する
		if( ! static::$modules):
			static::$modules = \Module::get_exists();
		endif;
		//アクションセットをモジュール名の配列で初期化する
		static::$actions[$module] = array();
	}

	/**
	 * Gets a singleton instance of Actionset
	 */
	public static function instance()
	{
		empty($module) and die('please forge.');
		if ( ! static::$instance)
		{
			static::$instance = static::forge($module);
		}
		return static::$instance;
	}

	/**
	 * forge()
	 */
	public static function forge($module = null)
	{
		return new static($module);
	}

	/**
	 * get_modules()
	 */
	public static function get_modules()
	{
		return static::$modules;
	}

	/**
	 * get_realms_from_module()
	 */
	protected static function get_realms_from_module($module)
	{
		if( ! static::$modules) die('please forge');
		$realms =array();
		foreach (glob(static::$modules[$module].'/classes/actionset/'.'*') as $actionfile):
			if( ! is_dir($actionfile)) continue;
			$realms[] = basename($actionfile);
		endforeach;
		return $realms;
	}

	/**
	 * get_valid_actionset_name()
	 */
	public static function get_valid_actionset_name($module = null, $realm = null)
	{
		is_null($module) and die('wrong module name for actionset');
		$module = ucfirst($module);
		return "\\$module\Actionset_".ucfirst($realm).'_'.$module;
	}

	/**
	 * get_module_actionset()
	 */
	public static function get_module_actionset($module, $obj = null)
	{
		if( ! \Module::loaded($module)){
			if( ! \Module::load($module)) die("module doesn't exist");
		}
		$realms = static::get_realms_from_module($module) ;

		//一つのモジュールについてすべてのアクションセットを取得する
		$actionsets = array();
		foreach($realms as $each_realm):
			if( ! isset(static::$actions[$module][$each_realm])){
				$l_realm = ucfirst($each_realm);
				$actionset_class = static::get_valid_actionset_name($module, $l_realm);
				if(class_exists($actionset_class)){
					$actionset_class::set_actionset($module, $each_realm, $obj);
				}
			}
		endforeach;

		if( ! isset(static::$actions[$module])) return false;

		//整形
		$retvals = static::$actions[$module];
		$overrides = array();
		foreach($retvals as $realm_name => $actions):
			foreach($actions as $action_k => $action):
				//prepare override
				if(isset($retvals[$realm_name][$action_k]['overrides'])){
					$overrides = array_merge($overrides, $retvals[$realm_name][$action_k]['overrides']);
				}
			endforeach;
			//orderを修正
			$retvals[$realm_name] = \Arr::multisort($retvals[$realm_name], array('order' => SORT_ASC,));
		endforeach;

		//override
		foreach($overrides as $realm_name => $urls):
			$retvals[$realm_name]['override_url'] = $urls;
		endforeach;

		return $retvals ? $retvals : false;
	}

	/**
	 * get_actionset()
	 */
	public static function get_actionset($module = null, $obj = null)
	{
		is_null($module) and $module = static::$modules;
		$modules = ! is_array($module) ? array($module => '') : $module ;

		$retvals = array();
		foreach($modules as $each_module => $path):
			if($retval = self::get_module_actionset($each_module, $obj)){
				$retvals[$each_module] = $retval;
			}
		endforeach;

		return $retvals ? $retvals : false ;
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
	 * generate_anchors()
	 */
	public static function generate_anchors($module, $action, $links, $obj, $exceptions = array())
	{
		static $exists = array();

		$urls = array();
		//check $exceptions
		if(in_array(\Request::main()->action, $exceptions)):
			return $urls;
		endif;

		//check auth
		$allowed = \Auth::auth($module.'/'.$action);
		if( ! $allowed){
			if( ! \Auth::owner_auth($module, $action, $obj)){
				return $urls;
			}
		}

		//urls
		foreach($links as $link):
			$url  = \Arr::get($link, 0, false);
			$str  = \Arr::get($link, 1, false);
			$attr = \Arr::get($link, 2, array());
			if(! $url || ! $str || in_array($url, $exists)) continue;
			$exists[] = $url;
			$urls[] = \Html::anchor($url, $str, $attr);
		endforeach;

		return $urls;
	}

	/**
	 * generate_bulk_anchors()
	 */
	public static function generate_bulk_anchors($module, $model, $opt, $nicename, $urls)
	{
		if(! $urls) return array();
		$target = join('/',array_slice(\Uri::segments(), 0, 3));
		$patterns[] = "{$module}/{$model}";
		$patterns[] = "{$module}/index_revision/{$model}";
		$patterns[] = "{$module}/each_index_revision/{$model}";
		$patterns[] = "{$module}/view_revision/{$model}";
		if(in_array($target, $patterns)):
			$override_urls['base'] = array(
				\Html::anchor("{$module}/{$model}/?create=1","{$nicename}新規作成"),
				\Html::anchor("{$module}/index_revision/{$model}?opt={$opt}","設定履歴"),
			);
		endif;
		return @$override_urls ?: array();
	}

	/**
	 * remove_actionset($realm)
	 */
	public static function remove_actionset($module, $realm)
	{
		unset(static::$actions[$module][$realm]);
	}

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
		if( ! isset(static::$actions[$module][$realm])):
			static::$actions[$module][$realm] = array();
		endif;
		static::$actions[$module][$realm][$name] = $arr;
	}

	/**
	 * set_actionset()
	 * static::$actionsを育てるmethod
	 */
	public static function set_actionset($module, $realm = null, $obj = null)
	{
		$methods = array_flip(get_class_methods(get_called_class()));
		$methods = \Arr::filter_prefixed($methods, 'actionset_');
		$methods = array_flip($methods);

		static::$actions[$module][$realm] = array();

		//primary key
		$obj = is_object($obj) ? $obj : (object) array() ;
		$id = method_exists($obj, 'get_pk') ? $obj->get_pk() : null ;

		foreach($methods as $method):
			$p_method = 'actionset_'.$method;
			if( ! method_exists(get_called_class(), $p_method)) continue;
			static::$actions[$module][$realm][$method] = static::$p_method($module, $obj, $id);
		endforeach;
	}
}
