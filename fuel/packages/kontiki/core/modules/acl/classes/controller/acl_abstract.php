<?php
namespace Kontiki;
abstract class Controller_Acl extends \Kontiki\Controller_Crud
{
	/**
	 * router()
	 * 
	 */
	public function router($method, $params)
	{
		parent::router($method, $params);
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
	 * auth()
	 */
	public static function auth($current_action = null, $userinfo = null)
	{
		//管理者は許可
		if(in_array(-2, $userinfo['usergroup_ids']) || in_array(-1, $userinfo['usergroup_ids'])) return true;

		//configのalways_allowed（ACLを通らないコントローラ）を確認
		if($current_action === null || $userinfo === null) return false;
		$always_allowed = \Config::get('always_allowed');
		if(in_array($current_action, $always_allowed)) return true;

		//リクエストされたURIを確認
		$uris = array();
		$is_allow = false;
		//リクエストされたURIのパターンを生成し確認
		foreach(\Uri::segments() as $param):
			$uris[] = $param;
			$str = join('/',$uris);
			if(in_array($str, @$userinfo['acls'])):
				$is_allow = true;
				break;
			endif;
		endforeach;
		if($is_allow) return true;

		//userinfoを確認
		return (in_array($current_action, @$userinfo['acls']));
	}

	/**
	 * owner_auth()
	 * オーナ権限はコントローラ依存性が強いので、各コントローラで実装。
	 * 原則、abstract controllerにあるが、個別の実装は、userモジュールのコントローラを参考にすること。
	 */
	public static function owner_auth($current_action = null, $userinfo = null)
	{
		//管理者は許可
		if(in_array(-2, $userinfo['usergroup_ids']) || in_array(-1, $userinfo['usergroup_ids'])) return true;

		//userinfoを確認
		if($current_action === null || $userinfo === null) return false;
		return in_array($current_action, @$userinfo['acls_ower']);
	}

	/**
	 * action_controller_index()
	 */
	public function action_controller_index()
	{
		//vals
		$controllers       = \Acl\Model_Acl::get_controllers();
		$controllers_owner = \Acl\Model_Acl::get_controllers($is_owner = 1);
		$usergroups        = \Acl\Model_Acl::get_usergroups();
		$users             = \Acl\Model_Acl::get_users();

		//view
		$view = \View::forge('controller_index');
		$view->set_global('title', 'アクセス権限管理');
		$view->set('controllers4acl',       $controllers);
		$view->set('controllers_owner4acl', $controllers_owner);
		$view->set('usergroups',            $usergroups);
		$view->set('users',                 $users);

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
		$actionsets  = \Acl\Model_Acl::get_controller_actionset($controller);

		foreach($actionsets as $k => $actionset):
			if(isset($actionset['is_admin_only']) && $actionset['is_admin_only'] == true) unset($actionsets->$k);
		endforeach;

		//check database
		$q = \DB::select('action');
		$q->from('acls');
		$q->where('controller', '=', $controller);
		if( ! is_null($usergroup_id)) $q->where('usergroup_id', '=', $usergroup_id);
		if( ! is_null($user_id)) $q->where('user_id', '=', $user_id);
		$q->where('owner_auth','=', null);
		$results = $q->execute()->as_array();
		$results = \Arr::flatten($results, '_');
		$aprvd_actionset = \Acl\Model_Acl::judge_set($results, $actionsets);

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
			\Session::set_flash('error', '必要項目を選択してから「次へ」を押してください');
			\Response::redirect(\Uri::create('/acl/controller_index/'));
		endif;

		//対象コントローラのオーナ向けアクションセットの取得
		$actionsets = \Acl\Model_Acl::get_controller_actionset($controller, $is_owner = 1);

		//check database
		$q = \DB::select('action');
		$q->from('acls');
		$q->where('controller', '=', $controller);
		$q->where('owner_auth', '=', '1');
		$results = $q->execute()->as_array();
		$results = \Arr::flatten($results, '_');
		$aprvd_actionset = \Acl\Model_Acl::judge_set($results, $actionsets);

		//view
		$controllers = \Acl\Model_Acl::get_controllers($is_owner = 1);
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
		if( ! \Security::check_token()) \Response::redirect(\Uri::create('/acl/controller_index/'));

		//user requests
		$controller   = \Input::param('controller') == 'none' ? null : \Input::param('controller') ;
		$usergroup_id = is_numeric(\Input::post('usergroup')) ? \Input::post('usergroup') : null;
		$user_id      = is_numeric(\Input::post('user')) ? \Input::post('user') : null;
		$acls         = \Input::post('acls');
		if($controller == null && ($usergroup_id == null || $user_id == null)):
			\Response::redirect(\Uri::create('/acl/controller_index/'));
		endif;

		//vals
		$actionsets = \Acl\Model_Acl::get_controller_actionset($controller);

		//query build
		if (\Input::method() == 'POST'):
			//まずすべて削除
			$q = \DB::delete('acls');
			$q->where('controller', '=', $controller);
			if( ! is_null($usergroup_id)) $q->where('usergroup_id', '=', $usergroup_id);
			if( ! is_null($user_id)) $q->where('user_id', '=', $user_id);
			$q->where('owner_auth', '=', null);
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
		$actionsets = \Acl\Model_Acl::get_controller_actionset($controller, $is_owner = 1);

		//query build
		if (\Input::method() == 'POST'):
			//まずすべて削除
			$q = \DB::delete('acls');
			$q->where('owner_auth', '=', '1');
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
							'owner_auth' => true
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
