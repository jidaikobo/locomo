<?php
namespace Locomo;
class View_Base extends \ViewModel
{
	/**
	* before()
	*/
	public function before()
	{
		//base_assign
		$this->base_assign();
		$this->include_asset();
		$this->get_controllers();
	}

	/**
	* base_assign()
	*/
	public function base_assign()
	{
		//site_title
		$this->get_view()->set_global('site_title', \Config::get('site_title'));

		//ユーザ情報
		$this->get_view()->set_global('userinfo', \Auth::get_user());
		$this->get_view()->set_global('is_user_logged_in', \Auth::is_user_logged_in());
		$this->get_view()->set_global('is_admin', false);
		$this->get_view()->set_global('is_root',  false);
		$this->get_view()->set_global('is_guest', false);
		if(\Auth::get_user_id() == -1):
			$this->get_view()->set_global('is_admin', true);
		endif;
		if(\Auth::get_user_id() == -2):
			$this->get_view()->set_global('is_root',  true);
			$this->get_view()->set_global('is_admin', true);
		endif;
		if(\Auth::get_user_id() == 0):
			$this->get_view()->set_global('is_guest', true);
			$this->get_view()->set_global('display_name', '');
		endif;
		
		//anti CSRF
		$this->get_view()->set_global('token_key', \Config::get('security.csrf_token_key'));
		$this->get_view()->set_global('token', \Security::fetch_token());

		//module and action
		$controller_class = \Request::main()->controller;
		$controller = \Inflector::denamespace($controller_class);		
		$controller = strtolower(substr($controller, 11));
		$action     = \Request::active()->action;

		//url
		$this->get_view()->set_global('controller', $controller);
		$this->get_view()->set_global('action', $action);
//		$this->get_view()->set_global('query_string', \Uri::create(\input::get()));
//		$this->get_view()->set_global('current_uri', \Uri::create('/'.$controller.'/'.$action.'/'));
		$this->get_view()->set_global('current_uri', \Uri::create('/'.$controller.'/'.$action.'/', array(), \input::get()));
		$this->get_view()->set_global('home_uri', \Uri::base());
		$this->get_view()->set_global('home_url', \Uri::base());
		
		//controller_name
		$controller_name = $controller_class::$nicename;
		$this->get_view()->set_global('controller_name', $controller_name);
		
		//body_class
		$class_arr = array(\Request::main()->route->module, \Request::main()->route->action );
		if( \Request::main()->route->action == 'login' && \Config::get('use_login_as_top') ) $class_arr[] = 'home';
		if(\Auth::is_user_logged_in()) $class_arr[] = 'loggedin';
		$this->get_view()->set_global('body_class', implode($class_arr,' '));

		//actionset
		$item = isset($this->_active_request->controller_instance->_single_item) ?
			$this->_active_request->controller_instance->_single_item : 
			null ;

		$actions = \Actionset::get_menu(
			$controller,
			$realm = 'all',
			$item,
			$get_authed_url = true,
			$exceptions = array(),
			$include_admin_only = true
		);
		$this->get_view()->set_global('actions', $actions);
	}

	/**
	* include_asset()
	* include用にオーバーライドのCSSとjsを取得するクロージャ
	*/
	public function include_asset()
	{
		$include_asset = function($file) {
			$override_file = \Uri::base().PROJECTVIEWDIR.'/'.$file;
			$default_file  = \Uri::base().'content/fetch_view/'.$file;
			$ret_file = file_exists(DOCROOT.'view/'.$file) ? $override_file : $default_file;
			return $ret_file;
		};
		$this->get_view()->set_global('include_asset', $include_asset);
	}

	/**
	* get_controllers()
	* コントローラメニュー用にコントローラを取得するクロージャ
	*/
	public function get_controllers()
	{
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
		$this->get_view()->set_global('get_controllers', $get_controllers);
	}

}
