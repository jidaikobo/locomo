<?php
namespace Locomo;
class View extends \Fuel\Core\View
{
	/**
	 * base_assign()
	 */
	public function base_assign($item = null)
	{
		//guest
		//body_class
		$class_arr = array(\Request::main()->route->module, \Request::main()->route->action );
		if( \Request::main()->route->action == 'login' && \Config::get('no_home') ) $class_arr[] = 'home';
		if(\Auth::check()) $class_arr[] = 'loggedin';
		$this->set_global('body_class', implode($class_arr,' '));

		//for users
		if( ! \Auth::check())
		{
			$this->set_global('locomo', array());
			return;
		}

		//controllers
		$mod_or_ctrl = \Request::main()->module ?: \Request::main()->controller;
		$ctrls = \Actionset::get_actionset($mod_or_ctrl, $item) ?: array();

		$locomo = array();
		$locomo['controllers'] = \Util::get_mod_or_ctrl();
		$locomo['current']['mod_or_ctrl'] = $mod_or_ctrl;
		$locomo['current']['controller'] = $ctrls;
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
