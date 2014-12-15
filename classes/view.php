<?php
namespace Locomo;
class View extends \Fuel\Core\View
{
	/**
	 * base_assign()
	 * @param object $obj use for auth. Fuel\Model object
	 */
	public function base_assign($obj = null)
	{
		// module and controller
		$module     = \Inflector::remove_head_backslash(\Request::main()->module);
		$controller = \Inflector::add_head_backslash(\Request::main()->controller);
		$action     = \Request::main()->action;

		// logo
		$logo = APPPATH.'locomo/system/img/logo.png';
		if ( ! file_exists($logo))
		{
			$logo = \Asset::img();
			$logo = LOCOMOPATH.'assets/img/system/logo.png';
		}

		// body_class
		$class_arr = array(
			'lcm_module_'.strtolower($module),
			'lcm_ctrl_'.strtolower(\Inflector::ctrl_to_safestr($controller)),
			'lcm_action_'.strtolower($action)
		);
		if ($action == 'login' && \Config::get('no_home'))
		{
			$class_arr[] = 'home';
		}
		if (\Auth::check())
		{
			$class_arr[] = 'loggedin';
		}
		$this->set_global('body_class', implode($class_arr, ' '));

		// locomo - for logged in users'
		$locomo = array();
		if ( ! \Auth::check())
		{
			$this->set_global('locomo', $locomo);
			return;
		}

		// locomo path
		$locomo['locomo_path'] = $controller.DS.$action;

		// current controller
		$locomo['controller']['name'] = $controller;
		$ctrl_home = \Arr::get($controller::$locomo, 'admin_home');
		$locomo['controller']['ctrl_home'] = $ctrl_home;
		$locomo['controller']['home'] = \Uri::create(\Inflector::ctrl_to_dir($ctrl_home));
		$locomo['controller']['home_name'] = \Arr::get($controller::$locomo, 'admin_home_name');
		$locomo['controller']['nicename'] = \Arr::get($controller::$locomo, 'nicename');

		// current module
		if ($module)
		{
			$config = \Config::load($module.'::'.$module, 'admin_bar', $reload = true);
			if ( ! \Arr::get($config, 'main_controller'))
			{
				new \OutOfBoundsException('module\'s config must contain main_controller value.');
			}

			$locomo['module']['name'] = $module;
			$ctrl_home = \Arr::get($config['main_controller']::$locomo, 'admin_home');
			$locomo['module']['ctrl_home'] = $ctrl_home;
			$locomo['module']['home'] = \Uri::create(\Inflector::ctrl_to_dir($ctrl_home));
			$locomo['module']['nicename'] = $config['nicename'];
			$locomo['module']['main_controller'] = $config['main_controller'];
		}

		// get accessible controller
		$all_ctrls = \Util::get_mod_or_ctrl();
		foreach($all_ctrls as $k => $v)
		{
			if ( ! \Auth::instance()->has_access(\Arr::get($v, 'admin_home')))
			{
				unset($all_ctrls[$k]);
			}
		}
		$locomo['controllers'] = $all_ctrls;

		$this->set_global('locomo', $locomo);

		// actionset
		$default = ['index' => [], 'base' => [], 'ctrl' => []];
		$actionset = \Actionset::get_actionset($controller, $obj, $default);
		$this->set_global('actionset', $actionset, false);
	}
}
