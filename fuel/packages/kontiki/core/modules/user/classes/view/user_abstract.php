<?php
namespace Kontiki;
abstract class View_User extends \Kontiki\View
{
	/**
	* view()
	* must use parent::view()
	*/
	public function view()
	{
		//parent::view()
		parent::view();

		//view
		$view = \View::forge();

		//このシステムのすべてのユーザグループ（選択肢用）
		$usergroups = \Usergroup\Model_Usergroup::find('all');
		$view->set_global('usergroups', $usergroups);
	}
}
