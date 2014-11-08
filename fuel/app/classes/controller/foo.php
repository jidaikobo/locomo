<?php
class Controller_Foo extends \Locomo\Controller_Crud
{
	public function action_index()
	{
		$view = \View::forge('foo/index');
		$view->base_assign();
		$view->set_global('title', 'hoge');
		$this->template->content = $view;
	}
}
