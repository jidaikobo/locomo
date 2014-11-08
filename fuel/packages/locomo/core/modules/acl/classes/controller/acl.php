<?php
namespace Acl;
class Controller_Acl extends \Locomo\Controller_Base
{
	/**
	 * action_controller_index()
	 */
	public function action_controller_index()
	{
		//vals
		$controllers       = \Acl\Model_Acl::get_controllers();
		$usergroups        = \Acl\Model_Acl::get_usergroups();
		$users             = \Acl\Model_Acl::get_users();

		//view
		$view = \View::forge('controller_index');
		$view->set_global('title', 'アクセス権限管理');
		$view->set('controllers4acl', $controllers);
		$view->set('usergroups',      $usergroups);
		$view->set('users',           $users);
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_actionset_index()
	 * 
	 */
	public function action_actionset_index()
	{
		//user requests
		$controller   = \Input::param('controller') == 'none' ? null : \Input::param('controller') ;
		$usergroup_id = is_numeric(\Input::param('usergroup')) ? \Input::param('usergroup') : null;
		$user_id      = is_numeric(\Input::param('user')) ? \Input::param('user') : null;

		if(($controller == null) || ($usergroup_id == null && $user_id == null)):
			\Session::set_flash('error', '必要項目を選択してから「次へ」を押してください。');
			return \Response::redirect(\Uri::create('/acl/controller_index/'));
		endif;

		//vals
		$controllers = \Acl\Model_Acl::get_controllers();
		$usergroups  = \Acl\Model_Acl::get_usergroups();
		$users       = \Acl\Model_Acl::get_users();
		$module      = trim(\Inflector::get_namespace($controller), '\\');
		$actionsets  = \Actionset::get_actionset($controller, $module);

		//check database
		$q = \DB::select('slug');
		$q->from('acls');
		$q->where('controller', '=', $controller);
		if( ! is_null($usergroup_id)) $q->where('usergroup_id', '=', $usergroup_id);
		if( ! is_null($user_id)) $q->where('user_id', '=', $user_id);
		$results = $q->execute()->as_array();
		$results = \Arr::flatten($results, '_');
		foreach($actionsets as $realm => $actionset):
			$aprvd_actionset[$realm] = \Acl\Model_Acl::judge_set($results, $actionset);
		endforeach;

		//view
		$view = \View::forge('actionset_index');
		$view->set_global('title', 'アクセス権限管理: アクション選択');
		$view->set('controller',        $controllers[$controller]);
		$view->set('usergroup',        @$usergroups[$usergroup_id]);
		$view->set('user',             @$users[$user_id]);
		$view->set('hidden_controller', $controller);
		$view->set('hidden_usergroup',  $usergroup_id);
		$view->set('hidden_user',       $user_id);
		$view->set('actionsets',        $actionsets);
		$view->set('aprvd_actionset',   $aprvd_actionset);
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_update_acl()
	 * 
	 */
	public function action_update_acl()
	{
		//CSRF
//		if( ! \Security::check_token()) \Response::redirect(\Uri::create('/acl/controller_index/'));

		//user requests
		$controller   = \Input::param('controller') == 'none' ? null : \Input::param('controller') ;
		$usergroup_id = is_numeric(\Input::post('usergroup')) ? \Input::post('usergroup') : null;
		$user_id      = is_numeric(\Input::post('user')) ? \Input::post('user') : null;
		$acls         = \Input::post('acls');
		if($controller == null && ($usergroup_id == null || $user_id == null)):
			\Response::redirect(\Uri::create('/acl/controller_index/'));
		endif;

		//vals
		$module      = trim(\Inflector::get_namespace($controller), '\\');
		$actionsets  = \Actionset::get_actionset($controller, $module);

		//query build
		if (\Input::method() == 'POST'):
			//まずすべて削除
			$q = \DB::delete('acls');
			$q->where('controller', '=', $controller);
			if( ! is_null($usergroup_id)) $q->where('usergroup_id', '=', $usergroup_id);
			if( ! is_null($user_id)) $q->where('user_id', '=', $user_id);
			$q->execute();

			//aclを更新
			if(is_array(\Input::post('acls'))):
				foreach($acls as $realm => $acl):
					foreach($acl as $action => $v):
						foreach($actionsets[$realm][$action]['dependencies'] as $each_action):
							//format conditions
							$conditions = \Auth_Acl_Locomoacl::_parse_conditions($each_action);
							$condition = serialize($conditions);

							//insert
							$q = \DB::insert('acls');
							$q->set(array(
								'module' => $conditions['module'],
								'controller' => $conditions['controller'],
								'action' => $conditions['action'],
								'slug' => $condition,
								'usergroup_id' => $usergroup_id,
								'user_id' => $user_id
								)
							);
							$q->execute();
						endforeach;
					endforeach;
				endforeach;
			endif;
		endif;

		//redirect
		$args = \input::all() ;
		unset($args['submit']);
		unset($args['acls']);
		\Session::set_flash('success', 'アクセス権をアップデートしました');
		\Response::redirect(\Uri::create('/acl/actionset_index/', array(), $args));
	}
}
