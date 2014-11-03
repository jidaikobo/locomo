<?php
namespace Locomo;
class View extends \Fuel\Core\View
{
	/**
	 * base_assign()
	 */
	public function base_assign($item = null)
	{
		//body_class
		$class_arr = array(\Request::main()->route->module, \Request::main()->route->action );
		if( \Request::main()->route->action == 'login' && \Config::get('use_login_as_top') ) $class_arr[] = 'home';
		if(\Auth::is_user_logged_in()) $class_arr[] = 'loggedin';
		$this->set_global('body_class', implode($class_arr,' '));

		//actionset
		$item = is_object($item) ? $item : (object) array();
		$actions = \Actionset::get_actionset(\Request::main()->module, $item);
		$this->set_global('actions', $actions, false);
	}

	/**
	 * get_controllers()
	 */
	public static function get_controllers($is_admin = false)
	{
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
		array_multisort($order, SORT_ASC, $controllers);
		return $controllers;
	}
}
