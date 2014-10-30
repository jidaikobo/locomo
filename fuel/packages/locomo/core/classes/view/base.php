<?php
namespace Locomo;
class View_Base extends \ViewModel
{
	/**
	* view()
	*/
	public function view()
	{
		//base_assign
		self::base_assign();
		self::include_asset();
		self::get_controllers();
//		self::get_actionset();
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
		$view->set_global('userinfo', \Auth::get_user());
		$view->set_global('is_user_logged_in', \Auth::is_user_logged_in());
		$view->set_global('is_admin', false);
		$view->set_global('is_root',  false);
		$view->set_global('is_guest', false);
		if(\Auth::get_user_id() == -1):
			$view->set_global('is_admin', true);
		endif;
		if(\Auth::get_user_id() == -2):
			$view->set_global('is_root',  true);
			$view->set_global('is_admin', true);
		endif;
		if(\Auth::get_user_id() == 0):
			$view->set_global('is_guest', true);
			$view->set_global('display_name', '');
		endif;
		
		//anti CSRF
		$view->set_global('token_key', \Config::get('security.csrf_token_key'));
		$view->set_global('token', \Security::fetch_token());

		//controller and action
		$controller_class = \Request::main()->controller;
		$controller = \Inflector::denamespace($controller_class);		
		$controller = strtolower(substr($controller, 11));
		$action     = \Request::active()->action;

		//url
		$view->set_global('controller', $controller);
		$view->set_global('action', $action);
//		$view->set_global('query_string', \Uri::create(\input::get()));
//		$view->set_global('current_uri', \Uri::create('/'.$controller.'/'.$action.'/'));
		$view->set_global('current_uri', \Uri::create('/'.$controller.'/'.$action.'/', array(), \input::get()));
		$view->set_global('home_uri', \Uri::base());
		$view->set_global('home_url', \Uri::base());
		
		//controller_name
		$controller_name = $controller_class::$nicename;
		$view->set_global('controller_name', $controller_name);
		
		//body_class
		$class_arr = array(\Request::main()->route->module, \Request::main()->route->action );
		if( \Request::main()->route->action == 'login' && \Config::get('use_login_as_top') ) $class_arr[] = 'home';
		if(\Auth::is_user_logged_in()) $class_arr[] = 'loggedin';
		$view->set_global('body_class', implode($class_arr,' '));
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
	* get_controllers()
	* コントローラメニュー用にコントローラを取得するクロージャ
	*/
	public function get_controllers()
	{
		$view = \View::forge();

		$get_controllers = function($is_admin = false) {
			//ログインした人向けのメニューなので、ゲストには何も返さない
			if( ! \Auth::is_user_logged_in()) return false;

			//対象モジュールを取得する
			$controllers = array();
			$userinfo = \Auth::get_userinfo();
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
					if(\Auth::get_user_id() <= -1):
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
//			array_multisort($order, SORT_ASC, $controllers);

			return $controllers;
		};
		$view->set_global('get_controllers', $get_controllers);
	}

}
