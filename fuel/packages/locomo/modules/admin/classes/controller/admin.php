<?php
namespace Admin;
class Controller_Admin extends \Locomo\Controller_Base
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => false,
		'order_at_menu' => 1000,
		'is_for_admin' => false,
		'admin_home' => '\\Admin\\Controller_Admin/home',
		'nicename' => '管理トップ',
	);

	/**
	* action_home()
	* toppgae
	*/
	public function action_home ($mod_or_ctrl = null)
	{
		$view = \View::forge('home');
		//if $mod_or_ctrl is null, show link to modules and controllers
		if (is_null($mod_or_ctrl))
		{
			$view->set('is_admin_home', true);
			$view->set_global('title', '管理ホーム');
		}else{
			//show actionset of target module and controller
			$actionset = \Actionset::get_actionset($mod_or_ctrl);
			$view->set('mod_or_ctrl', $actionset, false);

			//page title
			$mod_config = \Config::load($mod_or_ctrl.'::'.$mod_or_ctrl);
			$name = \Arr::get($mod_config, 'nicename') ?: $actionset[$mod_or_ctrl]['nicename'] ;
			$view->set_global('title', $name.' トップ');
		}
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	* action_dashboard()
	* dashboard
	*/
	public function action_dashboard()
	{
/*
メニューの一番上はダッシュボード
ログイン後、リダイレクト先がないときには、ここに来る。
ダッシュボードに何を出すかは、個々のユーザが決定するとよいとおもう。
モジュールやコントローラはHMVCでブロックを提供するものとする？
提供されるブロックは、action_fooでユーザにひもつけて登録する。
まずはテストモジュールの二つのコントローラについて、configを設定する
*/
		$view = \View::forge('dashboard');
		$view->set_global('title', 'ダッシュボード');
		$view->base_assign();
		$this->template->content = $view;
	}
}