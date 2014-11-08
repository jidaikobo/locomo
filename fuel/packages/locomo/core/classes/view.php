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
		if(\Auth::check()) $class_arr[] = 'loggedin';
		$this->set_global('body_class', implode($class_arr,' '));

		//actionset
		$actions = \Actionset::get_actionset(\Request::main()->controller, \Request::main()->module, $item);
		$this->set_global('actions', $actions, false);
	}

	/**
	 * get_controllers()
	 */
	public static function get_controllers($is_admin = false)
	{
		//ログインした人向けのメニューなので、ゲストには何も返さない
		if( ! \Auth::check()) return false;

		//すべてのコントローラのconfigを取得する
		$configs = \Util::get_all_configs();
		$controllers = array();
		$uris = array();
		foreach($configs as $config):
			//そもそもURLを持たないものを除外
			if(! \Arr::get($config, 'adminindex', false)) continue;

			//管理者向けは非管理者には表示しない
			if(\Arr::get($config, 'is_admin_only', false) && \Auth::get_user_id() <= -1) continue;

			//$is_admin条件があれば、管理者向けのみ表示。そうでなければ、非管理者向けを表示
			if( ! $is_admin && \Arr::get($config, 'is_admin_only', false)) continue;
			if( $is_admin && ! \Arr::get($config, 'is_admin_only', false)) continue;
			if( $is_admin && ! \Auth::is_admin()) continue;

			//すでにあるuriは足さない（オーバライドモジュール対策）
			$uri = \Uri::base().$config['adminindex'];
			if(in_array($uri, $uris)) continue;
			$uris[] = $uri;

			//links
			$links = array();
			$links['url']      = $uri;
			$links['index_nicename'] = $config['index_nicename'];

			//管理者はすべてのコントローラへのリンクを得る
			if(\Auth::get_user_id() <= -1):
				$controllers[] = $links;
			else:
				//管理者向けコントローラは表示しない
				if(@$settings['is_admin_only']) continue;

				//adminindexが許されていない場合は表示しない
/*
				if( ! in_array($url, $userinfo['acls'])) continue;
*/
				$controllers[] = $links;
			endif;
		endforeach;
		return $controllers;
	}

	/**
	 * get_active_request()
	 */
	public function get_active_request($item = null)
	{
		return $this->$item->active_request;
	}
}
