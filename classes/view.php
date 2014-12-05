<?php
namespace Locomo;
class View extends \Fuel\Core\View
{
	/**
	 * base_assign()
	 */
	public function base_assign($item = null)
	{
		// logo
		$logo = APPPATH.'locomo/system/img/logo.png';
		if ( ! file_exists($logo))
		{
			$logo = \Asset::img();
			$logo = LOCOMOPATH.'assets/img/system/logo.png';
		}

		//guest
		//body_class
		$class_arr = array(\Request::main()->route->module, \Request::main()->route->action );
		if (\Request::main()->route->action == 'login' && \Config::get('no_home') ) $class_arr[] = 'home';
		if (\Auth::check()) $class_arr[] = 'loggedin';
		$this->set_global('body_class', implode($class_arr,' '));

		//for users
		if ( ! \Auth::check())
		{
			$this->set_global('locomo', array());
			return;
		}

		//controllers
		$module = \Request::main()->module;
		$controller = \Request::main()->controller;
		$mod_or_ctrl = $module ?: $controller;
		$ctrls = \Actionset::get_actionset($mod_or_ctrl, $item) ?: array();

		$locomo = array();

		//check accessible controller
		$all_ctrls = \Util::get_mod_or_ctrl();
		foreach($all_ctrls as $k => $v):
			if ( ! \Auth::instance()->has_access(\Arr::get($v, 'admin_home'))) unset($all_ctrls[$k]);
		endforeach;

		$locomo['controllers'] = $all_ctrls;
		$locomo['current']['module']['name'] = $mod_or_ctrl;
		$locomo['current']['controller']['name'] = $controller;
		$locomo['current']['controller']['actionset'] = $ctrls;

		// controller home
		$ctrl_home = \Arr::get($controller::$locomo, 'admin_home');
		$ctrl_home = \Inflector::ctrl_to_dir($ctrl_home);
		$locomo['current']['controller']['home'] = \Uri::create($ctrl_home);
		$locomo['current']['controller']['nicename'] = \Arr::get($controller::$locomo, 'nicename');

		// module home
		$mod_config = \Config::load($module.'::'.$module);
		if ($module && $mod_config['main_controller'])
		{
			$ctrl_home = \Arr::get($mod_config['main_controller']::$locomo, 'admin_home');
			$ctrl_home = \Inflector::ctrl_to_dir($ctrl_home);
			$locomo['current']['module']['home'] = \Uri::create($ctrl_home);
			$locomo['current']['module']['nicename'] = $mod_config['nicename'];
		}

		$locomo['current']['mod_or_ctrl']['name'] = $mod_or_ctrl;

		$this->set_global('locomo', $locomo);

		//actionset
		$default = array('index' => array(), 'base' => array(), 'ctrl' => array());
		$controller = \Request::main()->controller;
		$actionset = \Arr::get($ctrls, "\\{$controller}.actionset") ?: $default;
		$this->set_global('actionset', $actionset, false);
	}

	/**
	 * get_active_request()
	 */
	public function get_active_request($item = null)
	{
		return $this->$item->active_request;
	}
}
