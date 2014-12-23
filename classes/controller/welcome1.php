<?php
namespace Locomo;
class Controller_Welcome1 extends Controller_Base
{
	// locomo
	public static $locomo = array(
		'show_at_menu' => false,
		'order_at_menu' => 1000,
		'is_for_admin' => false,
		'admin_home' => '\\Admin\\Controller_Admin/home',
		'nicename' => 'welcome',
	);

	public function action_index()
	{
		$view = \View::forge('welcome/index');
		$view->set_global('title', 'トップ');
		$view->base_assign();
		$this->template->content = $view;
	}

	public function action_index2()
	{
		$view = \View::forge('welcome/index');
		$view->set_global('title', 'index2');
		$view->base_assign();
		$this->template->content = $view;
	}
}
