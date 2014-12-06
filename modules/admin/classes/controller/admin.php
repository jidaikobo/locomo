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
		// if $mod_or_ctrl is null, show link to modules and controllers
		if (is_null($mod_or_ctrl))
		{
			$view->set('is_admin_home', true);
			$view->set_global('title', '管理ホーム');
		}
		else
		{
			// show actionset of target module and controller
			$mod_or_ctrl = \Inflector::remove_head_backslash($mod_or_ctrl);
			$actionset = \Actionset::get_actionset($mod_or_ctrl);

			//page title
			$mod_config = \Config::load($mod_or_ctrl.'::'.$mod_or_ctrl);
			$name = \Arr::get($mod_config, 'nicename') ?: $actionset[$mod_or_ctrl]['nicename'] ;

			// add 'admin_home' from controller::$locomo
			foreach ($actionset as $k => $v)
			{
				$locomo = $k::$locomo;
				$home      = \Arr::get($locomo, 'admin_home');
				$home_name = \Arr::get($locomo, 'admin_home_name', $name);
				$home_exp  = \Arr::get($locomo, 'admin_home_explanation', $name.'のトップです。');
				if ($home)
				{
					$args = array(
						'urls'        => array(\Html::anchor(\Inflector::ctrl_to_dir($home), $home_name)),
						'show_at_top' => true,
						'explanation' => $home_exp
					);
					array_unshift($actionset[$k]['actionset']['base'], $args);
				}
			}

			// assign
			$view->set('mod_or_ctrl', $actionset, false);
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
