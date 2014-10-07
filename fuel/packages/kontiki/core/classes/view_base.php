<?php
namespace Kontiki_Core;
class View_Base extends \ViewModel
{
	/**
	* view()
	*/
	public function view()
	{
		//base_assign
		self::base_assign();
		self::include_tpl();
		self::include_asset();
		self::get_controllers();
		self::get_actionset();
	}

	/**
	* base_assign()
	*/
	public static function base_assign()
	{
		$view = \View::forge();

		//site_title
		$view->set_global('site_title', \Config::get('site_title'));

		//ユーザ情報
		$view->set_global('userinfo', \User\Controller_User::$userinfo);
		$view->set_global('is_user_logged_in', \User\Controller_User::$is_user_logged_in);
		$view->set_global('is_admin', false);
		$view->set_global('is_root',  false);
		$view->set_global('is_guest', false);
		if(\User\Controller_User::$userinfo['user_id'] == -1):
			$view->set_global('is_admin', true);
		endif;
		if(\User\Controller_User::$userinfo['user_id'] == -2):
			$view->set_global('is_root',  true);
			$view->set_global('is_admin', true);
		endif;
		if(\User\Controller_User::$userinfo['user_id'] == 0):
			$view->set_global('is_guest', true);
		endif;

		//anti CSRF
		$view->set_global('token_key', \Config::get('security.csrf_token_key'));
		$view->set_global('token', \Security::fetch_token());

		//controller and action
		$controller = \Inflector::denamespace(\Request::main()->controller);
		$controller = strtolower(substr($controller, 11));
		$action     = \Request::active()->action;

		//url
		$view->set_global('controller', $controller);
		$view->set_global('action', $action);
//		$view->set_global('query_string', \Uri::create(\input::get()));
//		$view->set_global('current_uri', \Uri::create('/'.$controller.'/'.$action.'/'));
		$view->set_global('current_uri', \Uri::create('/'.$controller.'/'.$action.'/', array(), \input::get()));
		$view->set_global('home_uri', \Uri::base());
	}

	/**
	* include_asset()
	* include用にオーバーライドのCSSとjsを取得するクロージャ
	*/
	public function include_asset()
	{
		$view = \View::forge();

		$include_asset = function($file) {
			$override_file = \Uri::base().PROJECTVIEWDIR.'/'.$file;
			$default_file  = \Uri::base().'content/fetch_view/'.$file;
			$ret_file = file_exists(DOCROOT.'view/'.$file) ? $override_file : $default_file;
			return $ret_file;
		};
		$view->set_global('include_asset', $include_asset);
	}

	/**
	* include_tpl()
	* include用に指定テンプレートを取得するクロージャ
	*/
	public function include_tpl()
	{
		$view = \View::forge();

		$include_tpl = function($tpl) {
			$override_tpl = PKGPROJPATH.'views/'.$tpl;
			$default_tpl  = PKGCOREPATH.'views/'.$tpl;
			$ret_tpl = file_exists($override_tpl) ? $override_tpl : $default_tpl;
			return \View::forge($ret_tpl);
		};
		$view->set_global('include_tpl', $include_tpl);
	}


	/**
	* get_controllers()
	* コントローラメニュー用にコントローラを取得するクロージャ
	*/
	public function get_controllers()
	{
		$view = \View::forge();

		$get_controllers = function($is_admin = false) {
			//ログインした人向けのメニューなので、ゲストには何も返さない
			if( ! \User\Controller_User::$is_user_logged_in) return false;

			//対象モジュールを取得する
			$controllers = array();
			$userinfo = \User\Controller_User::$userinfo;
			$n = 0 ;
			foreach(\Config::get('module_paths') as $path):
				foreach (glob($path.'*') as $dirname):
					if( ! is_dir($dirname)) continue;

					//config
					$config = \Config::load($dirname.'/config/'.basename($dirname).'.php', true, true);
					if( ! $config) continue;
					if( ! $config['adminindex']) continue;

					$adminmodule = isset($config['adminmodule']) ?: false;
					if($is_admin && ! $adminmodule) continue;
					if( ! $is_admin && $adminmodule) continue;

					//adminindexへのurlを取得する
					$url = basename($dirname).'/'.$config['adminindex'];

					//すでにあるURLは足さない（オーバライドモジュールなんかは重複する）
					if(\Arr::in_array_recursive($url, $controllers)) continue;

					//links
					$links = array();
					$links['url']      = $url;
					$links['nicename'] = $config['nicename'];
					$links['order']    = $config['order_in_menu'];

					//管理者はすべてのコントローラへのリンクを得る
					if($userinfo['user_id'] <= -1):
						$controllers[$n] = $links;
					else:
						//管理者向けコントローラは表示しない
						if(@$settings['is_admin_only']) continue;
	
						//adminindexが許されていない場合は表示しない
						if( ! in_array($url, $userinfo['acls'])) continue;
						$controllers[$n] = $links;
					endif;
				$n++;
				endforeach;
			endforeach;

			//array_multisort
			foreach($controllers as $key => $row):
				$order[$key]  = $row['order'];
			endforeach;
			array_multisort($order, SORT_ASC, $controllers);

			return $controllers;
		};
		$view->set_global('get_controllers', $get_controllers);
	}

	/**
	* get_actionset()
	* コンテキストメニュー用にアクションセットを取得するクロージャ
	*/
	public function get_actionset()
	{
		$view = \View::forge();

		$get_actionset = function($controller, $item) {
			//ログインした人向けのメニューなので、ゲストには何も返さない
			if( ! \User\Controller_User::$is_user_logged_in) return false;

			//コントローラからactionsetを取得
			$controller_obj = \Kontiki\Util::get_valid_controller_name($controller);
			$obj = new $controller_obj(\Request::forge());
			$obj->set_actionset($controller, $item);

			//現在のURL（base urlをのぞく）
			$current = \Uri::string();

			//インデクス系のアクションセットを区別する
			$retvals            = array();
			$retvals['index']   = array();
			$retvals['control'] = array();
			foreach($obj::$actionset as $v):
				if( ! @$v['url']) continue;

				$key = (@$v['is_index']) ? 'index' : 'control';

				//urlはarrayの場合がある（workflowなど）
				if(is_array($v['url'])):
					foreach($v['url'] as $vv):
						$v['menu_str'] = $vv[0];//0がmenu_strで、1がurl
						if(substr($current, 0, strlen($vv[1])) == $vv[1]) continue;//not same url
						$retvals[$key][$vv[1]] = $v;
					endforeach;
				else:
					if($key == 'control' && substr($current, 0, strlen($v['url'])) == $v['url']) continue;//not same url at control
					$retvals[$key][$v['url']] = $v;
				endif;
			endforeach;

			return $retvals;
		};
		$view->set_global('get_actionset', $get_actionset);
	}
}
