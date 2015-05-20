<?php
namespace Locomo;
class Presenter_Header extends \Presenter
{
	public function view()
	{
		// module and controller
		$module     = \Inflector::remove_head_backslash(\Request::main()->module);
		$controller = \Inflector::add_head_backslash(\Request::main()->controller);
		$action     = \Request::main()->action;

		// custom icon and background
		$idty_class = '';
		$idty_class.=  ! file_exists(APPPATH.'locomo/assets/img/system/logo.png') ? ' default_logo' : '';
		$idty_class.=  ! file_exists(APPPATH.'locomo/assets/img/system/logo_s.png') ? ' default_logo_s' : '';
		$idty_class.=  ! file_exists(APPPATH.'locomo/assets/img/system/adminbar_bg.png') ? ' default_bg' : '';

		// body_class
		$class_arr = array(
			'lcm_module_'.strtolower($module),
			'lcm_ctrl_'.strtolower(\Inflector::ctrl_to_safestr($controller)),
			'lcm_action_'.strtolower($action),
			'lcm_browser_'.\Locomo\Browser::getBrowserType(),
			'lcm_ieversion_'.\Locomo\Browser::getIEVersion(),
		);
		if ($action == 'login' && \Config::get('no_home'))
		{
			$class_arr[] = 'home';
		}
		if (\Auth::check())
		{
			$class_arr[] = 'loggedin';
		}
		$this->_view->set_global('body_class', implode($class_arr, ' ').$idty_class);

		// data-uri
		$this->_view->set_global('body_data', 'data-uri='.\Uri::base(false));

		// affected_id for index template - from session
		$this->_view->set_global('affected_id', \Session::get_flash('affected_id'));
		
		// locomo - for logged in users'
		$locomo = array();
		if ( ! \Auth::check())
		{
			$this->_view->set_global('locomo', $locomo);
			return;
		}
		
		// locomo path
		$locomo['locomo_path'] = $controller.DS.$action;

		// ua - too heavy to use :-(
//		$locomo['ua']['browser'] = \Agent::browser();
//		$locomo['ua']['version'] = \Agent::version();
		
		// current controller
		$locomo['controller']['name'] = $controller;
		if (property_exists($controller, 'locomo') && \Arr::get($controller::$locomo, 'main_action'))
		{
			$main_action = \Arr::get($controller::$locomo, 'main_action');
			$locomo['controller']['main_action'] = $main_action;
			$locomo['controller']['main_url'] = \Uri::create(\Inflector::ctrl_to_dir($controller.DS.$main_action));
			$locomo['controller']['nicename'] = \Arr::get($controller::$locomo, 'nicename');
		}
		else
		{
			$locomo['controller']['main_action'] = false;
			$locomo['controller']['main_url'] = false;
			$locomo['controller']['nicename'] = false;
		}

		// current module
		if ($module)
		{
			$config = \Config::load($module.'::'.$module, 'admin_bar', $reload = true);

			$locomo['module']['name'] = $module;
			if (\Arr::get($config, 'main_controller'))
			{
				$main_action = \Arr::get($config['main_controller']::$locomo, 'main_action');
				$locomo['module']['main_action'] = $main_action;
				$locomo['module']['main'] = \Uri::create(\Inflector::ctrl_to_dir($main_action));
				$locomo['module']['nicename'] = $config['nicename'];
				$locomo['module']['main_controller'] = $config['main_controller'];
			}
			else
			{
				$locomo['module']['main_action'] = false;
				$locomo['module']['main'] = false;
				$locomo['module']['nicename'] = $module;
				$locomo['module']['main_controller'] = false;
			}
		}

		// get accessible controller
		\Profiler::mark('Locomo\\Presenter_Header::view() - get accessible controller');
		$all_ctrls = \Util::get_mod_or_ctrl();
		foreach($all_ctrls as $k => $v)
		{
			if ( ! \Auth::has_access(\Arr::get($v, 'main_action')))
			{
				unset($all_ctrls[$k]);
			}
		}
		$locomo['controllers'] = $all_ctrls;

		$this->_view->set_global('locomo', $locomo);

		// actionset
		$obj = $controller::get_object();
		\Profiler::mark('Locomo\\Presenter_Header::view() - actionset');
		\Actionset::get_actionset($controller, $obj);
		\Profiler::mark('Locomo\\Presenter_Header::view() - actionset done');

/*		
		$status = array();
		if (isset($obj->deleted_at) && ! is_null($obj->deleted_at))
		{
			$status[] = '削除済み';
		}
		
		
		if ($status)
		{
			$message = \Session::get_flash('message');
			\Session::set_flash('message', $status + $message);
		}
*/

	}
}
