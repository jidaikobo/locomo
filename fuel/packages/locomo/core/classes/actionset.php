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
	public static function get_realms_from_module($module)
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
	 * 継承してもよいが、内部からしか使わないこと
	 */
	protected static function get_module_actionset(
		$module = null,             //required
		$realm = null,              //strings or array, null means all realms
		$obj = null,                //to check auth
		$get_authed_url = false,    //to check auth
		$exceptions = array(),      //use with $realm == 'all'
		$include_admin_only = false //without set this switch, admin only will not be searched.
	)
	{
		if( ! \Module::loaded($module)){
			if( ! \Module::load($module)) die("module doesn't exist");
		}
		$realm = $realm == 'all' ? null : $realm ;
		$realms = $realm ? array($realm) : self::get_realms_from_module($module) ;

		if( ! $realms) return false;

		//一つのモジュールについてすべてのアクションセットを取得する
		$actionsets = array();
		foreach($realms as $each_realm):
			if( ! isset(static::$actions[$module][$realm])){
				$l_realm = ucfirst($each_realm);
				$actionset_class = self::get_valid_actionset_name($module, $l_realm);
				if(class_exists($actionset_class)){
					$actionset_class::set_actionset($module, $each_realm, $obj, $get_authed_url);
				}
			}
		endforeach;

		if( ! isset(static::$actions[$module])) return false;

		//整形
		$retvals = static::$actions[$module];
		foreach($retvals as $realm_name => $actions):
			//exceptionsによる除外
			if(in_array($realm_name, $exceptions)){
				unset($retvals[$realm_name]);
			}

			foreach($actions as $action_k => $action):
				//管理者向けのみのアクションセットを除外する
				if( ! $include_admin_only){
					if(isset($action['is_admin_only']) && $action['is_admin_only'] == true){
						unset($retvals[$realm_name][$action_k]);
					}
				}

				//realmが指定されていたらrealmを特定して返す
				if($realm && $realm != 'all' && $realm_name != $realm){
						unset($retvals[$realm_name]);
				}

				//orderを足しておく
				$retvals[$realm_name][$action_k]['order'] = isset($retvals[$realm_name][$action_k]['order']) ? $retvals[$realm_name][$action_k]['order'] : 1000 ;
			endforeach;

			//orderを修正
			$retvals[$realm_name] = \Arr::multisort($retvals[$realm_name], array(
			'order' => SORT_ASC,
			));
		endforeach;

		return $retvals ? $retvals : false;
	}

	/**
	 * get_actionset()
	 */
	public static function get_actionset(
		$module = null,             //strings or array, null means all modules
		$realm = null,              //strings or array, null means all realms
		$obj = null,                //to check auth
		$get_authed_url = false,    //to check auth
		$exceptions = array(),      //use with $realm == 'all'
		$include_admin_only = false //without set this switch, admin only will not be searched.
	)
	{
		is_null($module) and $module = static::$modules;
		is_null($realm) and $realm = 'all';

		$modules = ! is_array($module) ? array($module => '') : $module ;

		$retvals = array();
		foreach($modules as $each_module => $path):
			if(
				$retval = self::get_module_actionset(
					$each_module, $realm, $obj, $get_authed_url, $exceptions, $include_admin_only
				)
			){
				$retvals[$each_module] = $retval;
			}
		endforeach;

		//任意のrealmが存在しない場合
		if( ! isset($retvals[$each_module][$realm])){
			$retvals[$each_module][$realm] = array();
		}

		return $retvals ? $retvals : false ;
	}

	/**
	 * get_menu()
	 */
	public static function get_menu(
		$module = null,             //strings or array, null means all modules
		$realm = null,              //strings or array, null means all realms
		$obj = null,                //to check auth
		$get_authed_url = false,    //to check auth
		$exceptions = array(),      //use with $realm == 'all'
		$include_admin_only = false //without set this switch, admin only will not be searched.
	)
	{
		$actionsets = static::get_actionset(
			$module,
			$realm,
			$obj,
			$get_authed_url,
			$exceptions,
			$include_admin_only
		);
		if(! $actionsets) return false;

		//generate_realm_array()
		$retvals = array();
		foreach($actionsets as $module => $each_actionsets):
			foreach($each_actionsets as $each_realm => $actionset):
				$retvals[$module][$each_realm] = static::generate_realm_array($actionset);
			endforeach;
		endforeach;

		//指定されたものを返す
		if($module == null && $realm == 'all') return $retvals;
		if($module && $realm == 'all') return $retvals[$module];
		if($module && $realm) return $retvals[$module][$realm];
		if( ! $module) return $retvals[$module];
		return $retvals;
	}

	/**
	 * generate_realm_array()
	 */
	public static function generate_realm_array($actionsets)
	{
		$current = \Uri::string();
		$retvals = array();
		foreach($actionsets as $v):
			if( ! isset($v['url'])  || empty($v['url'])) continue;

			//urlはarrayの場合がある（workflowなど）
			if(is_array($v['url'])):
				foreach($v['url'] as $vv):
					//remove stashes
					$vv[1] = substr($vv[1], 0, 1) == '/' ? substr($vv[1], 1) : $vv[1];
					$vv[1] = substr($vv[1], -1) == '/' ? substr($vv[1], 0, -1) : $vv[1];

					$v['menu_str'] = $vv[0];//0がmenu_strで、1がurl
//					if(substr($current, 0, strlen($vv[1])) == $vv[1]) continue;//not same url
					$retvals[$vv[1]] = $v;
				endforeach;
			else:
				//remove stashes
				$v['url'] = substr($v['url'], 0, 1) == '/' ? substr($v['url'], 1) : $v['url'];
				$v['url'] = substr($v['url'], -1) == '/' ? substr($v['url'], 0, -1) : $v['url'];
//				if(substr($current, 0, strlen($v['url'])) == $v['url']) continue;//not same url at control
				$retvals[$v['url']] = $v;
			endif;
		endforeach;
		return $retvals;
	}

	/**
	 * generate_menu_html()
	 */
	public static function generate_menu_html($obj, $ul_attr = array(), $anchor_attr = array())
	{
		if( ! $obj) return false;

		$arr = array();
		foreach($obj as $url => $v):
			if( ! $url || ! $v['menu_str']) continue;
			$anchor_attr_loop = $anchor_attr;
			$confirm_str = "return confirm('{$v['menu_str']}をしてよろしいですか？')";
			$anchor_attr_loop = @$v['confirm'] ?
				$anchor_attr_loop + array('onclick' => $confirm_str) :
				$anchor_attr_loop;
			$arr[] = \Html::anchor(\Uri::base().$url, $v['menu_str'], $anchor_attr_loop);
		endforeach;

		if( ! $arr) return false;

		return \Html::ul($arr, $ul_attr);
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
	 * \Actionset::add_actionset('options', 'back', array('menu_str'=>'foo','url' => '/'));
	 */
	public static function add_actionset(
		$module,
		$realm = null,
		$name = null,
		$arr = array()
	)
	{
		if( ! isset(static::$actions[$module][$realm])):
			static::$actions[$module][$realm] = (object) array();
		endif;
		static::$actions[$module][$realm]->{$name} = $arr;
	}

	/**
	 * set_actionset()
	 * static::$actionsを育てるmethod
	 */
	public static function set_actionset($module, $realm = null, $obj = null, $get_authed_url = false)
	{
		$methods = array_flip(get_class_methods(get_called_class()));
		$methods = \Arr::filter_prefixed($methods, 'actionset_');
		$methods = array_flip($methods);

		static::$actions[$module][$realm] = array();

		foreach($methods as $method):
			$p_method = 'actionset_'.$method;
			if( ! method_exists(get_called_class(), $p_method)) continue;
			static::$actions[$module][$realm][$method] =
				static::$p_method($module, $obj, $get_authed_url);
		endforeach;
	}

	/**
	 * check_auth()
	 * @return  bool
	 */
	public static function check_auth($module, $action)
	{
		return \Acl\Controller_Acl::auth($module.'/'.$action, \Auth::get_userinfo());
	}

	/**
	 * check_owner_auth()
	 * @return  bool
	 */
	public static function check_owner_auth($module, $action, $obj)
	{
		return \Acl\Controller_Acl::owner_auth($module, $action, $obj, \Auth::get_userinfo()) ;
	}
}
