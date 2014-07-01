<?php
namespace User;
class View_User extends \Kontiki\View_User_Abstract
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

		//現在のユーザが所属するグループ
		$item = $view->get('item', null);
		if($item):
			$user_id = intval($item->id);
			$q = \DB::select('usergroup_id');
			$q->from('users_usergroups_r');
			$q->where('user_id', $user_id);
			$resuls = $q->execute()->as_array();
			$item->usergroups = $resuls ? \Arr::flatten_assoc($resuls) : array();
			$view->set_global('item', $item);
		endif;
	}
}
