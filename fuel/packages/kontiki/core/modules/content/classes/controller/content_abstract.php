<?php
namespace Kontiki;
abstract class Controller_Content extends \Kontiki\Controller_Crud
{
	/**
	 * router()
	 * 
	 */
	public function router($method, $params)
	{
		$allowed_action = array(
			'home',
			'404',
		);
		if( ! in_array($method, $allowed_action)):
			return \Response::redirect('/', 'location', 404);
		else:
			$action = 'action_'.$method;
			return $this->$action($params);
		endif;
	}

	/**
	 * set_actionset()
	 */
	public function set_actionset($controller = null, $id = null)
	{
		parent::set_actionset();
		self::$actionset = array();
		self::$actionset_owner = array();
	}

	/**
	* acl()
	* contents allowed to all user
	*/
	public function acl($userinfo)
	{
		return true;
	}

	/**
	* action_home()
	* toppgae
	*/
	public function action_home()
	{
		$view = \View::forge('home');
		$view->set_global('title', \Config::get('site_title'));
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	* action_404()
	* 404
	*/
	public function action_404()
	{
		$view = \View::forge('404');
		$view->set_global('title', 'page not found');
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}
}
