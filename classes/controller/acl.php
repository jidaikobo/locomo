<?php
namespace Locomo;
class Controller_Acl extends \Controller_Base
{
	// locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 50,
		'is_for_admin' => true,
		'no_acl' => true,
		'admin_home' => '\\Controller_Acl/controller_index',
		'nicename' => 'アクセス権',
		'actionset_methods' =>array(
			'base'   => array(
				'actionset_controller_index',
				'actionset_actionset_index'
			),
		),
	);

	/**
	 * actionset_controller_index()
	 */
	public static function actionset_controller_index($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'アクセス権管理',
			'show_at_top'  => true,
			'order'        => 1,
		);
		return $retvals;
	}

	/**
	 * actionset_actionset_index()
	 */
	public static function actionset_actionset_index($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => $urls ,
			'show_at_top'  => false,
			'action_name'  => 'アクセス権管理',
			'help'         => '
# 依存関係について
依存した行為を許可すると、自動的にほかの行為が許可される場合があります。たとえば「項目を編集する権利」を持った人は、「通常項目を閲覧する権利」が自動的に許可されます。

# ログインユーザ権限
「ログインユーザすべて」に行為を許可している場合、個別にアクセス権を与えなくても、許可された状態になっていることがあります。
',
			'order'        => 1,
		);
		return $retvals;
	}

	/**
	 * action_controller_index()
	 */
	public function action_controller_index()
	{
		// vals
		$mod_or_ctrl = \Model_Acl::get_mod_or_ctrl();
		$usergroups  = \Model_Acl::get_usergroups();
		$users       = \Model_Acl::get_users();

		// view
		$view = \View::forge('acl/controller_index');
		$view->set_global('title', 'コントローラ選択');
		$view->set('mod_or_ctrl', $mod_or_ctrl);
		$view->set('usergroups',  $usergroups);
		$view->set('users',       $users);
		$this->base_assign();
		$this->template->content = $view;
	}

	/**
	 * get_actionset()
	 * 
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

		// eliminate empty dependencies realm
		foreach ($actionsets as $k => $v)
		{
			foreach ($v as $kk => $vv)
			{
				foreach ($vv as $kkk => $vvv)
				{
					if ( ! \Arr::get($vvv, 'dependencies')) unset($actionsets[$k][$kk][$kkk]);
				}
				if(empty($actionsets[$k][$kk])) unset($actionsets[$k][$kk]);
			}
		}
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

		if (($mod_or_ctrl == null) || ($usergroup_id == null && $user_id == null))
		{
			\Session::set_flash('error', '必要項目を選択してから「次へ」を押してください。');
			return \Response::redirect(\Uri::create('/acl/controller_index/'));
		}

		// vals
		$controllers = \Model_Acl::get_mod_or_ctrl();
		$usergroups  = \Model_Acl::get_usergroups();
		$users       = \Model_Acl::get_users();

		// actionset
		$actionsets = static::get_actionset($mod_or_ctrl);

		// check database
		$q = \DB::select('slug');
		$q->from('lcm_acls');
		$q->where('controller', 'IN', array_keys($actionsets));
		if ( ! is_null($usergroup_id)) $q->where('usergroup_id', '=', $usergroup_id);
		if ( ! is_null($user_id)) $q->where('user_id', '=', $user_id);
		$q->or_where('usergroup_id', '=', '-10');
		$results = $q->execute()->as_array();
		$results = \Arr::flatten($results, '_');
		foreach($actionsets as $controller => $each_actionsets)
		{
			foreach($each_actionsets as $realm => $actionset)
			{
				$aprvd_actionset[$controller][$realm] = \Model_Acl::judge_set($results, $actionset);
			}
		}

		// target controller name
		$ctrl_strs = array();
		foreach($actionsets as $controller => $each_actionsets)
		{
			$ctrl_strs[] = $controller::$locomo['nicename'];
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
		$this->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_update_acl()
	 * 
	 */
	public function action_update_acl()
	{
		// CSRF
//		if ( ! \Security::check_token()) \Response::redirect(\Uri::create('/acl/controller_index/'));

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
			// delete all at first
			$q = \DB::delete('lcm_acls');
			$q->where('controller', 'IN', array_keys($actionsets));
			if ( ! is_null($usergroup_id)) $q->where('usergroup_id', '=', $usergroup_id);
			if ( ! is_null($user_id)) $q->where('user_id', '=', $user_id);
			$q->execute();

			// update acl
			if (is_array(\Input::post('acls')))
			{
				foreach($acls as $ctrl => $acl)
				{
					foreach($acl as $realm => $each_acls)
					{
						foreach($each_acls as $action => $v)
						{
							if ( ! \Arr::get($actionsets[$ctrl], $realm)) continue;
							foreach($actionsets[$ctrl][$realm][$action]['dependencies'] as $each_action)
							{
								//format conditions
								$conditions = \Auth_Acl_Locomoacl::_parse_conditions($each_action);
								$condition = serialize($conditions);

								//insert
								$q = \DB::insert('lcm_acls');
								$q->set(array(
									'controller' => \Inflector::add_head_backslash($conditions['controller']),
									'action' => $conditions['action'],
									'slug' => $condition,
									'usergroup_id' => $usergroup_id,
									'user_id' => $user_id
									)
								);
								$q->execute();
							}
						}
					}
				}
			}
		}

		// redirect
		$args = \input::all() ;
		unset($args['submit']);
		unset($args['acls']);
		\Session::set_flash('success', 'アクセス権をアップデートしました');
		\Response::redirect(\Uri::create('/acl/actionset_index/', array(), $args));
	}
}
