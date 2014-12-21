<?php
namespace Admin;
class Controller_Admin extends \Locomo\Controller_Base
{
	// locomo
	public static $locomo = array(
		'show_at_menu' => false,
		'order_at_menu' => 1000,
		'is_for_admin' => false,
		'admin_home' => '\\Admin\\Controller_Admin/home',
		'nicename' => '管理トップ',
		'actionset_classes' =>array(
			'option' => '\\Admin\\Actionset_Option_Admin',
		),
		'widgets' =>array(
			array('name' => 'コントローラ一覧', 'uri' => '\\Admin\\Controller_Admin/home'),
			array('name' => '現在時刻', 'uri' => '\\Admin\\Controller_Admin/clock'),
			array('name' => 'カレンダ', 'uri' => '\\Schedule\\Controller_Schedule/calendar'),
			array('name' => '顧客', 'uri' => '\\Controller_Customer/index_admin'),
		),
	);

	/**
	* action_home()
	* toppgae
	*/
	public function action_home ($ctrl = null)
	{
		$view = \View::forge('home');

		// if $ctrl is null, show link to modules and controllers
		if (is_null($ctrl))
		{
			$view->set('is_admin_home', true);
			$view->set_global('title', '管理ホーム');
		}
		else
		{
			// show actionset of target module and controller
			$ctrl = \Inflector::safestr_to_ctrl($ctrl);
			$module = \Inflector::get_modulename($ctrl);

			// is module's main controller or normal app controller?
			if ($module)
			{
				// module
				$actionset = \Actionset::get_module_actionset($module);
				$config = \Config::load($module.'::'.$module, 'action_home', true);
				if ( ! \Arr::get($config, 'nicename') ||  ! \Arr::get($config, 'main_contoller'))
				{
					new \OutOfBoundsException('module\'s config must contain main_controller value.');
				}
				$main_contoller = \Arr::get($config, 'main_contoller');
				$name = \Arr::get($config, 'nicename', '') ;
			}
			else
			{
				// this is not a module
				$actionset[$ctrl] = \Actionset::get_actionset($ctrl);
				$locomo = $ctrl::$locomo ;
				$name = \Arr::get($locomo, 'nicename');
			}

			// if $actionset is not exist
			if(! $actionset || ! $actionset[$ctrl])
			{
				$actionset = array($ctrl => array(
					'base' => array()
				));
			}

			// add 'admin_home' action to actionset from controller::$locomo
			if($actionset)
			{
				foreach ($actionset as $k => $v)
				{
					$locomo = $k::$locomo;
					$home      = \Arr::get($locomo, 'admin_home');
					$home_name = \Arr::get($locomo, 'admin_home_name', $name);
					$home_exp  = \Arr::get($locomo, 'admin_home_explanation', $name.'のトップです。');
					if ($home)
					{
						$args = array(
							'urls'        => array(\Html::anchor(\Inflector::ctrl_to_dir($home), $home_name)),
							'show_at_top' => true,
							'explanation' => $home_exp
						);
						array_unshift($actionset[$k]['base'], $args);
					}
				}
			}

			// assign
			$view->set('actionset', $actionset, false);
			$view->set_global('title', $name.' トップ');
		}
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	* action_dashboard()
	* dashboard
	*/
	public function action_dashboard()
	{
		$objs = \Admin\Model_Dashboard::find('all', array('where'=>array(array('user_id'=>\Auth::get('id')))));

		// set fall-back widgets
		if ( ! $objs)
		{
			$configs = \Config::get('default_dashboard') ?: array();
			$objs = array();
			foreach ($configs as $config)
			{
				$objs[] = (object) $config;
			}
		}

		// prepare widget nicename
		$widget_names = array();
		foreach (\Util::get_mod_or_ctrl() as $k => $v)
		{
			if ( ! $widget = \Arr::get($v, 'widgets')) continue;
			$widget_names += \Arr::assoc_to_keyval($widget, 'name', 'uri');
		}

		// set to position
		$actions = array();
		foreach ($objs as $k => $obj)
		{
			$act = $obj->action;
			$acts = explode('?', $act);

			// auth
			if( ! \Auth::instance()->has_access($acts[0])) continue;

			// querystring 
			$qstr = \Arr::get($acts, 1);

			// method exists?
			$ctrl_act = \Locomo\Auth_Acl_Locomoacl::_parse_conditions($acts[0]);
			if ( ! class_exists($ctrl_act['controller']))
			{
				$mod_ctrl = explode('\\', $ctrl_act['controller']);
				if ( ! \Module::load($mod_ctrl[1])) continue;
			}
			if ( ! method_exists($ctrl_act['controller'], 'action_'.$ctrl_act['action'])) continue;

			// hmvc
			$actions[$k]['content'] = \Request::forge(\Inflector::ctrl_to_dir($acts[0]))->execute(array($qstr));
			$actions[$k]['size'] = $obj->size ?: 1 ;// default small
			$actions[$k]['title'] = array_search($act, $widget_names);
		}

		// assign
		$view = \View::forge('dashboard');
		$view->set_global('title', 'ダッシュボード');
		$view->set_safe('actions', $actions);
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	* action_edit()
	* edit dashboard items
	*/
	public function action_edit($user_id = null)
	{
		// get workflow name
		$this->model_name = \Auth::is_admin() ? '\\Admin\\Model_Admin' : '\\Admin\\Model_User';
		parent::action_edit(\Auth::get('id'));

		// add_actionset - back to index at edit
		$action['urls'][] = \Html::anchor('/admin/admin/dashboard/','ダッシュボードへ');
		$action['order'] = 10;
		\Actionset::add_actionset($this->request->controller, 'ctrl', $action);

		// assign
		$content= \View::forge('edit_dashboard');
		$content->set_global('title', 'ダッシュボードの設定');
		$content->base_assign(); // to override add_actionset()
		$this->template->content = $content;
	}

	/**
	* action_clock()
	*/
	public function action_clock()
	{
		$view = \View::forge('clock');
		$view->set_global('title', 'アナログ時計');
		$view->base_assign();
		$this->template->content = $view;
	}

}
