<?php
namespace Kontiki;
abstract class Controller_Acl_Abstract extends \Kontiki\Controller
{
	/**
	 * router()
	 * 
	 */
	public function router($method, $params)
	{
		$banned_action = array(
			'index',
			'view',
		);
		if(in_array($method, $banned_action)):
			return \Response::redirect('/', 'location', 404);
		else:
			$action = 'action_'.$method;
			return $this->$action($params);
		endif;
	}

	/**
	 * set_actionset()
	 * 
	 */
	public function set_actionset()
	{
		parent::set_actionset();
	}

	/**
	 * auth()
	 * 
	 */
	public static function auth($controller = null, $action = null, $userinfo = null)
	{
		//false
		if($controller === null || $action === null) return false;
		if($userinfo === null) return false;

		//always guest allowed controllers
		$always_allowed = \Config::get('always_allowed');
		$check_str = $controller.'/'.$action;
		if(in_array($check_str, $always_allowed)) return true;

		//admin and root user is always allowed
		if(in_array(-2, $userinfo['usergroup_ids']) || in_array(-1, $userinfo['usergroup_ids'])) return true;

		//check acl
		$q = \DB::select('controller');
		$q->from('acls');
		$q->where('controller', $controller);
		$q->where('action', $action);
		if( ! empty($userinfo['usergroup_ids'])):
			$q->where('usergroup_id','IN' , $userinfo['usergroup_ids']);
		else:
			$q->where('user_id', $userinfo['user_id']);
		endif;
		$result = $q->execute()->current() ;

		return ($result) ? true : false ;
	}

	/**
	 * action_controller_index()
	 * 
	 */
	public function action_controller_index()
	{
		//vals
		$controllers = \Acl\Model_Acl::get_controllers();
		$usergroups  = \Acl\Model_Acl::get_usergroups();
		$users       = \Acl\Model_Acl::get_users();

		//view
		$view = \View::forge('controller_index');
		$view->set_global('title', 'アクセス権限管理');
		$view->set('controllers', $controllers);
		$view->set('usergroups', $usergroups);
		$view->set('users', $users);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
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
			\Response::redirect(\Uri::create('/acl/controller_index/'));
		endif;

		//vals
		$controllers = \Acl\Model_Acl::get_controllers();
		$usergroups  = \Acl\Model_Acl::get_usergroups();
		$users       = \Acl\Model_Acl::get_users();
		$actionsets  = \Kontiki\Actionset::actionItems($controller);

		//check database
		$q = \DB::select('action');
		$q->from('acls');
		$q->where('controller', '=', $controller);
		if($usergroup_id) $q->where('usergroup_id', '=', $usergroup_id);
		if($user_id) $q->where('user_id', '=', $user_id);
		$q->where('owner_allowed','=', null);
		$results = $q->execute()->as_array();
		$results = \Arr::flatten($results, '_');
		$aprvd_actionset = \Kontiki\Actionset::judge_set($results, $actionsets);

		//view
		$view = \View::forge('actionset_index');
		$view->set_global('title', 'アクセス権限管理: アクション選択');
		$view->set('controller', $controllers[$controller]);
		$view->set('usergroup', @$usergroups[$usergroup_id]);
		$view->set('user', @$users[$user_id]);
		$view->set('hidden_controller', $controller);
		$view->set('hidden_usergroup', $usergroup_id);
		$view->set('hidden_user', $user_id);
		$view->set('actionsets', $actionsets);
		$view->set('aprvd_actionset', $aprvd_actionset);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_actionset_owner_index()
	 * 
	 */
	public function action_actionset_owner_index()
	{
		//user requests
		$controller   = \Input::param('controller') == 'none' ? null : \Input::param('controller') ;
		if($controller == null):
			\Session::set_flash('error', '必要項目を選択してから「次へ」を押してください。');
			\Response::redirect(\Uri::create('/acl/controller_index/'));
		endif;

		//vals - get owner allowed actionset
		$controllers = \Acl\Model_Acl::get_controllers();
		$actionsets  = (object) array();
		foreach(\Kontiki\Actionset::actionItems($controller) as $actionset_name => $action):
			if( ! isset($action['owner_allowed'])) continue;
			$actionsets->{$actionset_name} = $action;
		endforeach;

		//check database
		$q = \DB::select('action');
		$q->from('acls');
		$q->where('controller', '=', $controller);
		$q->where('owner_allowed', '=', '1');
		$results = $q->execute()->as_array();
		$results = \Arr::flatten($results, '_');
		$aprvd_actionset = \Kontiki\Actionset::judge_set($results, $actionsets);

		//view
		$view = \View::forge('actionset_owner_index');
		$view->set_global('title', 'オーナ用アクセス権限管理: アクション選択');
		$view->set('controller', $controllers[$controller]);
		$view->set('hidden_controller', $controller);
		$view->set('hidden_owner', 1);
		$view->set('actionsets', $actionsets);
		$view->set('aprvd_actionset', $aprvd_actionset);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
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
		$actionsets = \Kontiki\Actionset::actionItems($controller);

		//query build
		if (\Input::method() == 'POST'):
			//まずすべて削除
			$q = \DB::delete('acls');
			if($usergroup_id) $q->where('usergroup_id', '=', $usergroup_id);
			if($user_id) $q->where('user_id', '=', $user_id);
			$q->where('owner_allowed', '=', null);
			$q->execute();

			//aclを更新
			if(is_array(\Input::post('acls'))):
				foreach($acls as $action => $v):
					foreach($actionsets->{$action}['dependencies'] as $each_action):
						$q = \DB::insert('acls');
						$q->set(array(
							'controller' => $controller,
							'action' => $each_action,
							'usergroup_id' => $usergroup_id,
							'user_id' => $user_id
							)
						);
						$q->execute();
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

	/**
	 * action_update_owner_acl()
	 * 
	 */
	public function action_update_owner_acl()
	{
		//CSRF
		if( ! \Security::check_token()) \Response::redirect(\Uri::create('/acl/controller_index/'));

		//user requests
		$controller   = \Input::post('controller');
		$acls         = \Input::post('acls');
		if($controller == null) \Response::redirect(\Uri::create('/acl/controller_index/'));

		//vals
		$actionsets = \Kontiki\Actionset::actionItems($controller);

		//query build
		if (\Input::method() == 'POST'):
			//まずすべて削除
			$q = \DB::delete('acls');
			$q->where('owner_allowed', '=', '1');
			$q->execute();

			//aclを更新
			if(is_array(\Input::post('acls'))):
				foreach($acls as $action => $v):
					foreach($actionsets->{$action}['dependencies'] as $each_action):
						$q = \DB::insert('acls');
						$q->set(array(
							'controller' => $controller,
							'action' => $each_action,
							'usergroup_id' => null,
							'user_id' => null,
							'owner_allowed' => true
							)
						);
						$q->execute();
					endforeach;
				endforeach;
			endif;
		endif;

		//redirect
		$args = \input::all() ;
		unset($args['submit']);
		unset($args['acls']);
		\Session::set_flash('success', 'アクセス権をアップデートしました');
		\Response::redirect(\Uri::create('/acl/actionset_owner_index/', array(), $args));
	}
}
