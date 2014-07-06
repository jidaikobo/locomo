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
