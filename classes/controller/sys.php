<?php
namespace Locomo;
class Controller_Sys extends \Controller_Base
{
	// locomo
	public static $locomo = array(
		'nicename'     => 'システム', // for human's name
		'main_action'  => 'admin', // main action
		'show_at_menu' => false, // true: show at admin bar and admin/home
		'is_for_admin' => false, // true: hide from admin bar
		'order'        => 0, // order of appearance
		'no_acl'       => true, // true: admin's action. it will not appear at acl.
		'widgets' =>array(
			array('name' => 'コントローラ一覧', 'uri' => '\\Controller_Sys/admin'),
			array('name' => '現在時刻', 'uri' => '\\Controller_Sys/clock'),
			array('name' => 'カレンダ', 'uri' => '\\Schedule\\Controller_Schedule/calendar'),
		),
	);

	/**
	* action_home()
	* toppgae
	*/
	public function action_home()
	{
		// このアクションはトップページ専用として、sys/homeへのアクセスはできないようにする。
		if (substr(\Uri::string(),0,12) == 'sys/home'):
			return \Response::redirect('/', 'location', 404);
		endif;

		// 描画
		$view = \View::forge('sys/home');
		$view->set_global('title', \Config::get('slogan'));
		$this->template->content = $view;
	}

	/**
	* action_404()
	* 404
	*/
	public function action_404()
	{
		$this->_template = 'default';
		$view = \View::forge('sys/404');
		$view->set_global('title', 'Not Found');
		$this->template->content = $view;
	}

	/**
	* action_403()
	* 403
	*/
	public function action_403()
	{
		$view = \View::forge('sys/403');
		$view->set_global('title', 'Forbidden');
		$this->template->content = $view;
	}

	/**
	 * action_fetch_view()
	 * fetch files view
	 */
	public function action_fetch_view()
	{
		// ヘンなアクセスを追い返す
		$ext = \Input::extension();
		$args = func_get_args();
		$path = join('/', $args).'.'.$ext;
		if (empty($path) || empty($ext))
		{
			return \Response::redirect('/', 'location', 404);
		}

		// 存在確認
		$filename = '' ;
		$locomo_assets = LOCOMOPATH."assets/{$path}";
		$app_assets = APPPATH."locomo/assets/{$path}";

		$filename = file_exists($locomo_assets) ? $locomo_assets : '';
		$filename = file_exists($app_assets)    ? $app_assets : $filename;

		if ( ! $filename)
		{
			$page = \Request::forge('sys/404')->execute();
			return new \Response($page, 404);
		}

		// 拡張子を確認
		$config = \Config::load('upload');
		$ext = strtolower($ext);
		if ( ! isset($config['mime_whitelist'][$ext])) return \Response::forge();

		// profilerをoffに
		\Fuel\Core\Fuel::$profiling = false;

		// 描画
		$headers = array('Content-type' => $config['mime_whitelist'][$ext]);
		$this->template->set_global('title', '');
		return \Response::forge(file_get_contents($filename), 200, $headers);
	}

	/**
	* action_admin()
	* controller menu
	*/
	public function action_admin($ctrl = null)
	{
		$view = \View::forge('sys/admin');

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
				$config = \Config::load($module.'::'.$module, 'action_admin', true);
				if ( ! \Arr::get($config, 'nicename') || ! \Arr::get($config, 'main_contoller'))
				{
					new \OutOfBoundsException('module\'s config must contain main_controller value.');
				}
				$name = \Arr::get($config, 'nicename', $module) ;
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
					$home      = \Arr::get($k::$locomo, 'main_action');
					$home_name = \Arr::get($k::$locomo, 'main_action_name', $name);
					$home_exp  = \Arr::get($k::$locomo, 'main_action_explanation', $name.'のトップです。');
					$actionset[$k]['order'] = \Arr::get($k::$locomo, 'order', 10);
					if ($home)
					{
						$url       = $k.'/'.$home;
						$args = array(
							'urls'        => array(\Html::anchor(\Inflector::ctrl_to_dir($url), $home_name)),
							'show_at_top' => true,
							'explanation' => $home_exp
						);
						array_unshift($actionset[$k]['base'], $args); // add main action to top of base realm
					}
				}
				// order
				$actionset = \Arr::multisort($actionset, array('order' => SORT_ASC));
			}

			// assign
			$view->set('actionset', $actionset, false);
			$view->set_global('title', $name.' トップ');
		}
		$this->template->content = $view;
	}

	/**
	* action_dashboard()
	* dashboard
	*/
	public function action_dashboard()
	{
		$objs = \Model_Dashboard::find('all', array('where'=>array(array('user_id'=>\Auth::get('id')))));

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
/*
hmvcにサイズを渡すと、widget側でサイズごとの表示を返すことができるようなので、そのような措置を考える。
*/
			$actions[$k]['content'] = \Request::forge(\Inflector::ctrl_to_dir($acts[0]))->execute(array($qstr));
			$actions[$k]['size'] = $obj->size ?: 1 ;// default small
			$actions[$k]['title'] = array_search($act, $widget_names);
		}

		// assign
		$view = \View::forge('sys/dashboard');
		$view->set_global('title', 'ダッシュボード');
		$view->set_safe('actions', $actions);
		$this->template->content = $view;
	}

	/**
	* action_edit()
	* edit dashboard items
	*/
	public function action_edit($user_id = null)
	{
		// get workflow name
		$this->model_name = \Auth::is_admin() ? '\\Model_Dashboard_Admin' : '\\Model_Dashboard_User';
		parent::edit(\Auth::get('id'));

		// add_actionset - back to index at edit
		$action['urls'][] = \Html::anchor('/sys/dashboard/','ダッシュボードへ');
		$action['order'] = 10;
		\Actionset::add_actionset($this->request->controller, 'ctrl', $action);

		// assign
		$content= \View::forge('sys/edit_dashboard');
		$content->set_global('title', 'ダッシュボードの設定');
		$this->base_assign(); // to override add_actionset()
		$this->template->content = $content;
	}

	/**
	* action_clock()
	*/
	public function action_clock()
	{
		$view = \View::forge('sys/clock');
		$view->set_global('title', 'アナログ時計');
		$this->template->content = $view;
	}
}