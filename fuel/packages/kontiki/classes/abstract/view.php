<?php
namespace Kontiki;
abstract class ViewModel extends \ViewModel
{
	/**
	* base_assign()
	* base assign
	*/
	public static function base_assign()
	{
		//base assign
		$view = \View::forge();

		//anti CSRF
		$view->set_global('token_key', \Config::get('security.csrf_token_key'));
		$view->set_global('token', \Security::fetch_token());

		//controller and action
		$controller = \Inflector::denamespace(\Request::main()->controller);
		$controller = strtolower(substr($controller, 11));
		$action     = \Request::active()->action;

		//url
		$view->set_global('controller', $controller);
		$view->set_global('action', $action);
//		$view->set_global('query_string', \Uri::create(\input::get()));
//		$view->set_global('current_uri', \Uri::create('/'.$controller.'/'.$action.'/'));
		$view->set_global('current_uri', \Uri::create('/'.$controller.'/'.$action.'/', array(), \input::get()));

		//include template closure
		$include_tpl = function($tpl) {
			$override_tpl = PKGPATH.'kontiki/views/'.$tpl;
			$default_tpl  = PKGPATH.'kontiki/views_base/'.$tpl;
			$ret_tpl = file_exists($override_tpl) ? $override_tpl : $default_tpl;
			return \View::forge($ret_tpl);
		};
		$view->set_global('include_tpl', $include_tpl);
	}

	/**
	* view()
	*/
	public function view()
	{
		//base_assign
		self::base_assign();
	}
}
