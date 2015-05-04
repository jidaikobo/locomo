<?php
namespace Locomo;
class Controller_Acl extends \Controller_Base
{
	// locomo
	public static $locomo = array(
		'nicename'     => 'アクセス権', // for human's name
		'explanation'  => 'ユーザやユーザグループにコントローラへのアクセス権を付与します。',
		'main_action'  => 'controller_index', // main action
		'main_action_name' => 'アクセス権管理', // main action's name
		'main_action_explanation' => 'ユーザやユーザグループにコントローラへのアクセス権を付与します。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => true, // true: hide from admin bar
		'order'        => 1050, // order of appearance
		'no_acl'       => true, // true: admin's action. it will not appear at acl.
	);

	/**
	 * action_controller_index()
	 */
	public function action_controller_index()
	{
		$view = \View::forge('acl/controller_index');
		$view->set_global('title', 'コントローラ選択');
		$this->template->content = $view;
	}

	/**
	 * get_actionset()
	 */
	private static function get_actionset($mod_or_ctrl)
	{
		$ctrl = \Inflector::safestr_to_ctrl($mod_or_ctrl);
		$module = \Inflector::get_modulename($ctrl);
		if ($module)
		{
			$actionsets = \Actionset::get_module_actionset($module);
		}
		else
		{
			$actionsets[$ctrl] = \Actionset::get_actionset($ctrl);
		}
		if(empty($actionsets)) throw new \OutOfBoundsException('actionset not found');
		return $actionsets;
	}

	/**
	 * action_actionset_index()
	 * 
	 */
	public function action_actionset_index()
	{
		// user requests
		$mod_or_ctrl  = \Input::param('mod_or_ctrl', null) ?: \Input::param('mod_or_ctrl') ;
		$usergroup_id = is_numeric(\Input::param('usergroup')) ? \Input::param('usergroup') : null;
		$user_id      = is_numeric(\Input::param('user')) ? \Input::param('user') : null;

		if (($mod_or_ctrl == null) || ($mod_or_ctrl == 'none') || ($usergroup_id == null && $user_id == null))
		{
			\Session::set_flash('error', '必要項目を選択してから「次へ」を押してください。');
			return \Response::redirect(\Uri::create('/acl/controller_index/'));
		}

		// vals
		$controllers = \Model_Acl::get_mod_or_ctrl();
		$usergroups  = \Model_Acl::get_usergroups();
		$users       = \Model_Usr::get_users();

		// actionset
		$actionsets = static::get_actionset($mod_or_ctrl);

		// ユーザから所属ユーザグループを取得する
		$usergroup_ids = array();
		if ($user_id && ! $usergroup_id)
		{
			$usergroup_ids = \Model_Usrgrp::find('all',
				array(
					'related' => 'user',
					'where' => array(array('user.id', '=', $user_id))
				)
			);
			$usergroup_ids = array_keys($usergroup_ids);
		}

		// check database
		$q = \DB::select('slug');
		$q->from('lcm_acls');
//		$q->where('controller', 'IN', array_keys($actionsets)); //これがあるとコントローラをまたがってACLを与えられない
		if ($usergroup_ids && $user_id)
		{
			$q->where('usergroup_id', 'IN', $usergroup_ids);
			$q->or_where('user_id', '=', $user_id);
		} else {
			if ( ! is_null($usergroup_id)) $q->where('usergroup_id', '=', $usergroup_id);
			if ( ! is_null($user_id)) $q->where('user_id', '=', $user_id);
		}
		$q->or_where('usergroup_id', '=', '-10');
		$results = $q->execute()->as_array();
		$results = \Arr::flatten($results, '_');

		// Configで許されているアクションを取得
		$always_allowed = \Config::get('always_allowed', array());
		$always_user_allowed = \Config::get('always_user_allowed', array());
		$results = array_merge($results, $always_allowed, $always_user_allowed);

		// judge_set()
		foreach($actionsets as $controller => $v)
		{
			$aprvd_actionset[$controller] = \Model_Acl::judge_set($controller, $results);
		}

		// target controller name
		$ctrl_strs = array();
		foreach($actionsets as $controller => $each_actionsets)
		{
			$ctrl_strs[] = \Util::get_locomo($controller, 'nicename');
		}

		// view
		$view = \View::forge('acl/actionset_index');
		$view->set_global('title', 'アクション選択');
		$view->set('ctrl_str',          join(', ',$ctrl_strs));
		$view->set('mod_or_ctrl',       $mod_or_ctrl);
		$view->set('usergroup',        @$usergroups[$usergroup_id]);
		$view->set('user',             @$users[$user_id]);
		$view->set('hidden_controller', $controller);
		$view->set('hidden_usergroup',  $usergroup_id);
		$view->set('hidden_user',       $user_id);
		$view->set('actionsets',        $actionsets);
		$view->set('aprvd_actionset',   $aprvd_actionset);
		$this->template->content = $view;
	}

	/**
	 * action_update_acl()
	 * 
	 */
	public function action_update_acl()
	{
		// user requests
		$mod_or_ctrl  = \Input::param('mod_or_ctrl') == 'none' ? null : \Input::param('mod_or_ctrl');
		$usergroup_id = is_numeric(\Input::post('usergroup')) ? \Input::post('usergroup') : null;
		$user_id      = is_numeric(\Input::post('user')) ? \Input::post('user') : null;
		$acls         = \Input::post('acls');
		if ($mod_or_ctrl == null && ($usergroup_id == null || $user_id == null))
		{
			\Response::redirect(\Uri::create('/acl/controller_index/'));
		}

		// actionsets
		$actionsets = static::get_actionset($mod_or_ctrl);

		// query build
		if (\Input::method() == 'POST')
		{
			// CSRF
//			if ( ! \Security::check_token()) \Response::redirect(\Uri::create('/acl/controller_index/'));

			// delete all at first
			foreach ($actionsets as $ctrl => $actionset)
			{
				foreach ($actionset as $action_name => $v)
				{
					if ( ! isset($v['dependencies'])) continue;
					$q = \DB::delete('lcm_acls');
					$q->where('slug', 'IN', $v['dependencies']);
					if ( ! is_null($usergroup_id)) $q->where('usergroup_id', '=', $usergroup_id);
					if ( ! is_null($user_id)) $q->where('user_id', '=', $user_id);
					$q->execute();
				}
			}

			// update acl
			if (is_array($acls))
			{
				foreach($acls as $ctrl => $acl)
				{
					foreach($acl as $action => $v)
					{
						if ( ! \Arr::get($actionsets[$ctrl], $action)) continue;
						if ( ! isset($actionsets[$ctrl][$action]['dependencies'])) continue;

						foreach($actionsets[$ctrl][$action]['dependencies'] as $each_action)
						{
							//format conditions
							$each_action = \Inflector::add_head_backslash($each_action);
							list($dpnd_controller, $dpnd_action) = explode(DS, $each_action);

							//insert
							$q = \DB::insert('lcm_acls');
							$q->set(array(
								'controller'   => $dpnd_controller,
								'action'       => $dpnd_action,
								'slug'         => $each_action,
								'usergroup_id' => $usergroup_id,
								'user_id'      => $user_id
								)
							);
							$q->execute();
						}
					}
				}
			}
		}

		// redirect
		$args = \input::all() ;
		unset($args['submit']);
		unset($args['locomo_csrf_token']);
		unset($args['acls']);
		\Session::set_flash('success', 'アクセス権をアップデートしました');
		\Response::redirect(\Uri::create('/acl/actionset_index/', array(), $args));
	}
}
