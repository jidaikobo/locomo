<?php
class Controller_Foo extends \Locomo\Controller_Crud
{
	//locomo
	public static $locomo = array(
		'nicename' => 'fooコントローラ',
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
