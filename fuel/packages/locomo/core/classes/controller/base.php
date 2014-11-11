<?php
namespace Locomo;
class Controller_Base extends \Fuel\Core\Controller_Hybrid
{
	/**
	* @var string name for human
	*/
	public static $nicename = '';

	/**
	* @var string template
	*/
	public $template = 'default';

	/**
	 * @var string model name
	 */
	protected $model_name = '';

	/**
	 * @var config
	 */
	public static $config = array();

	/**
	* before()
	*/
	public function before()
	{
		// parent
		parent::before();

		// add template path
		$request = \Request::active();
		$request->add_path(PKGPROJPATH.'views'.DS.$this->request->module,true);
		$request->add_path(PKGPROJPATH.'modules'.DS.$this->request->module,true);

		// show profile to root only
		\Fuel::$profiling = \Auth::get_user_id() == -2 ?: false ;

		// load config and set model_name
		$controller = substr(ucfirst(\Inflector::denamespace($this->request->controller)), 11);
		if($this->request->module)
		{
			$module = ucfirst($this->request->module);
			$this->model_name = '\\'.$module.'\\Model_'.$module;
			static::$config = \Config::load(strtolower($this->request->module));
		}else{
			$this->model_name = '\\Model_'.$controller;
			static::$config = \Config::load(strtolower($controller));
		}

		// nicename
		static::$nicename = @static::$config['nicename'];
	}

	/**
	 * router()
	*/
	public function router($method, $params)
	{
		// action not exists
		$is_allow = true;
		if(
			! method_exists(get_called_class(), 'action_'.$method) &&
			! method_exists(get_called_class(), 'get_'.$method)
		){
			$is_allow = false;
			\Session::set_flash('error','"'.htmlspecialchars($method, ENT_QUOTES).'" is not exist.');
		}

		if( ! $is_allow):
			$page = \Request::forge('content/content/403')->execute();
			return new \Response($page, 403);
		endif;

		// use login as a toppage
		$use_login_as_top = \Config::get('use_login_as_top');
		if(
			$use_login_as_top && // config
			\Auth::get('id') == 0 && // for guest
			$this->request->module.DS.$method == 'content/home' // when toppage
		):
			return \Response::redirect(\Uri::create('user/auth/login'));
		endif;

		return parent::router($method, $params);
	}


	/**
	 * after()
	 */
	public function after($response)
	{
		// check auth
		if( ! \Auth::instance()->has_access($this->request->module.DS.'\\'.$this->request->controller.DS.$this->request->action.DS)):
			$page = \Request::forge('content/content/403')->execute();
			return new \Response($page, 403);
		endif;

		return parent::after($response);
	}
}

