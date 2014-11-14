<?php
class Controller_Bar_Bar extends \Locomo\Controller_Crud
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 10,
		'is_for_admin' => false,
		'admin_home' => '\\Controller_Bar/index_admin',
		'nicename' => 'barコントローラ',
		'actionset_classes' =>array(
			'base'   => '\\Actionset_Foo',
		),
	);

	/**
	 * action_index()
	 */
	public function action_index()
	{
		$view = \View::forge('foo/index');
		$view->base_assign();
		$view->set_global('title', 'hoge');
		$this->template->content = $view;
	}
}
