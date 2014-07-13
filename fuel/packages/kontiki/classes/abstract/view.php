<?php
namespace Kontiki;
abstract class ViewModel extends \ViewModel
{
	/**
	* base_assign()
	* base assign
	*/
	public static function base_assign()
	{
		//base assign
		$view = \View::forge();

		//ユーザ情報
		$view->set_global('userinfo', \User\Controller_User::$userinfo);
		$view->set_global('is_user_logged_in', \User\Controller_User::$is_user_logged_in);

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

		//include用にオーバーライドのCSSとjsを取得するクロージャ
		$include_asset = function($file) {
			$override_file = DOCROOT.'views/'.$file;
			$default_file  = DOCROOT.'views/default/'.$tpl;
			$ret_file = file_exists($override_file) ? $override_file : $default_file;
			return $ret_file;
		};
		$view->set_global('include_tpl', $include_tpl);

		//include用に指定テンプレートを取得するクロージャ
		$include_tpl = function($tpl) {
			$override_tpl = PKGPATH.'kontiki/views/'.$tpl;
			$default_tpl  = PKGPATH.'kontiki/views_default/'.$tpl;
			$ret_tpl = file_exists($override_tpl) ? $override_tpl : $default_tpl;
			return \View::forge($ret_tpl);
		};
		$view->set_global('include_tpl', $include_tpl);

		//コントローラメニュー用にコントローラを取得するクロージャ
		$get_controllers = function() {
			//ログインした人向けのメニューなので、ゲストには何も返さない
			if( ! \User\Controller_User::$is_user_logged_in) return false;

			//packageconfigから対象モジュールを取得する
			$controllers = array();
			$userinfo = \User\Controller_User::$userinfo;
			foreach(\Config::get('modules') as $controller => $settings):
				//adminindexへのurlを取得する
				$url = $controller.'/'.$settings['adminindex'];

				//管理者はすべてのコントローラへのリンクを得る
				if($userinfo['user_id'] <= -1):
					$controllers[$url] = $settings['nicename'];
				else:
					//管理者向けコントローラは表示しない
					if($settings['is_admin_only']) continue;

					//adminindexが許されていない場合は表示しない
					if( ! in_array($url, $userinfo['acls'])) continue;
					$controllers[$url] = $settings['nicename'];
				endif;
			endforeach;

			return $controllers;
		};
		$view->set_global('get_controllers', $get_controllers);

		//コンテキストメニュー用にアクションセットを取得するクロージャ
		$get_actionset = function($controller, $item) {
			//ログインした人向けのメニューなので、ゲストには何も返さない
			if( ! \User\Controller_User::$is_user_logged_in) return false;

			//コントローラからactionsetを取得
			$controller_ucfirst = ucfirst($controller);
			$controller_lower = strtolower($controller);
			$controller = "\\$controller_ucfirst\Controller_".$controller_ucfirst;
			$obj = new $controller(\Request::forge());
			$obj->set_actionset($controller_lower, $item);

			//インデクス系のアクションセットを区別する
			$retvals            = array();
			$retvals['index']   = array();
			$retvals['control'] = array();
			foreach($obj::$actionset as $v):
				if( ! $v['url']) continue;
				if(isset($v['is_index'])):
					$retvals['index'][] = $v;
				else:
					$retvals['control'][] = $v;
				endif;
			endforeach;

			//URLが同じアクションをまとめる
			$retvals['index']   = \Arr::assoc_to_keyval($retvals['index'], 'url', 'menu_str');
			$retvals['control'] = \Arr::assoc_to_keyval($retvals['control'], 'url', 'menu_str');

			return $retvals;
		};
		$view->set_global('get_actionset', $get_actionset);
	}

	/**
	* view()
	*/
	public function view()
	{
		//base_assign
		self::base_assign();
	}
}
