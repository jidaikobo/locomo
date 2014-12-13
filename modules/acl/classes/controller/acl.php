<?php
namespace Acl;
class Controller_Acl extends \Locomo\Controller_Base
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 50,
		'is_for_admin' => true,
		'admin_home' => '\\Acl\\Controller_Acl/controller_index',
		'nicename' => 'アクセス権',
		'help'     => 'packages/locomo/modules/acl/help/acl.html',
		'actionset_classes' =>array(
			'base'   => '\\Acl\\Actionset_Base_Acl',
		),
	);

	/**
	 * action_controller_index()
	 */
	public function action_controller_index()
	{
		//vals
		$mod_or_ctrl = \Acl\Model_Acl::get_mod_or_ctrl();
		$usergroups  = \Acl\Model_Acl::get_usergroups();
		$users       = \Acl\Model_Acl::get_users();

		//view
		$view = \View::forge('controller_index');
		$view->set_global('title', 'コントローラ選択');
		$view->set('mod_or_ctrl', $mod_or_ctrl);
		$view->set('usergroups',  $usergroups);
		$view->set('users',       $users);
		$view->base_assign();
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
		//user requests
		$mod_or_ctrl  = \Input::param('mod_or_ctrl', null) ?: \Input::param('mod_or_ctrl') ;
		$usergroup_id = is_numeric(\Input::param('usergroup')) ? \Input::param('usergroup') : null;
		$user_id      = is_numeric(\Input::param('user')) ? \Input::param('user') : null;

		if (($mod_or_ctrl == null) || ($usergroup_id == null && $user_id == null)):
			\Session::set_flash('error', '必要項目を選択してから「次へ」を押してください。');
			return \Response::redirect(\Uri::create('/acl/controller_index/'));
		endif;

		//vals
		$controllers = \Acl\Model_Acl::get_mod_or_ctrl();
		$usergroups  = \Acl\Model_Acl::get_usergroups();
		$users       = \Acl\Model_Acl::get_users();

		//actionset
		$actionsets = static::get_actionset($mod_or_ctrl);

		//check database
		$q = \DB::select('slug');
		$q->from('acls');
		$q->where('controller', 'IN', array_keys($actionsets));
		if ( ! is_null($usergroup_id)) $q->where('usergroup_id', '=', $usergroup_id);
		if ( ! is_null($user_id)) $q->where('user_id', '=', $user_id);
		$results = $q->execute()->as_array();
		$results = \Arr::flatten($results, '_');
		foreach($actionsets as $controller => $each_actionsets):
			foreach($each_actionsets as $realm => $actionset):
				$aprvd_actionset[$controller][$realm] = \Acl\Model_Acl::judge_set($results, $actionset);
			endforeach;
		endforeach;

		//対象コントローラ文字列
		$ctrl_strs = array();
		foreach($actionsets as $controller => $each_actionsets):
			$ctrl_strs[] = $controller::$locomo['nicename'];
		endforeach;

		//view
		$view = \View::forge('actionset_index');
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
//		if ( ! \Security::check_token()) \Response::redirect(\Uri::create('/acl/controller_index/'));

		//user requests
		$mod_or_ctrl  = \Input::param('mod_or_ctrl') == 'none' ? null : \Input::param('mod_or_ctrl') ;
		$usergroup_id = is_numeric(\Input::post('usergroup')) ? \Input::post('usergroup') : null;
		$user_id      = is_numeric(\Input::post('user')) ? \Input::post('user') : null;
		$acls         = \Input::post('acls');
		if ($mod_or_ctrl == null && ($usergroup_id == null || $user_id == null)):
			\Response::redirect(\Uri::create('/acl/controller_index/'));
		endif;

		//actionsets
		$actionsets = static::get_actionset($mod_or_ctrl);

		//query build
		if (\Input::method() == 'POST'):
			//まずすべて削除
			$q = \DB::delete('acls');
			$q->where('controller', 'IN', array_keys($actionsets));
			if ( ! is_null($usergroup_id)) $q->where('usergroup_id', '=', $usergroup_id);
			if ( ! is_null($user_id)) $q->where('user_id', '=', $user_id);
			$q->execute();

			//aclを更新
			if (is_array(\Input::post('acls'))):
				foreach($acls as $ctrl => $acl):
					foreach($acl as $realm => $each_acls):
						foreach($each_acls as $action => $v):
							if ( ! \Arr::get($actionsets[$ctrl], $realm)) continue;
							foreach($actionsets[$ctrl][$realm][$action]['dependencies'] as $each_action):
								//format conditions
								$conditions = \Auth_Acl_Locomoacl::_parse_conditions($each_action);
								$condition = serialize($conditions);

								//insert
								$q = \DB::insert('acls');
								$q->set(array(
									'controller' => \Inflector::add_head_backslash($conditions['controller']),
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
