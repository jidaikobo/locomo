<?php
namespace Locomo;
class Controller_Base extends \Fuel\Core\Controller_Rest
{
	/**
	* @var string name for human
	*/
	public static $nicename = '';

	/**
	* @var string template
	*/
	public $_template = 'default';

	/**
	 * @var string model name
	 */
	protected $model_name = '';

	/**
	* @var string current_action
	*/
	protected $current_action = '';

	/**
	 * @var config
	 */
	protected $config = array();

	/**
	* @var string current_id
	*/
	public static $current_id = '';

	/**z
	 * @var array set by self::set_actionset()
	 */
	public static $actionset = array();
	public static $actionset_owner = array();

	/**
	* @var test datas
	* array fieldname => 'type(text|email|int|date|datetime|geometry)'
	*/
	protected $test_datas = array();

	protected static $views_path_module = '';

	/**
	* before()
	*/
	public function before()
	{





		//テンプレートの検索パスを追加
		$request = \Request::active();

		$views_path_module = static::$views_path_module ?: $this->request->module;

		$request->add_path(PKGPROJPATH.'views'.DS.$views_path_module.DS,true);
		$request->add_path(PKGPROJPATH.'modules'.DS.$views_path_module.DS,true);

		//ユーザ情報のセット
		\Auth::set_userinfo();

		//profile表示はrootだけ（当然ながらConfigでtrueだったら計測はされる）
		\Fuel::$profiling = \Auth::get_user_id() == -2 ?: false ;

		//current_actionのセット
		//HMVCの場合は、呼ばれたモジュールに応じたものにかわる
		$this->current_action = $this->request->module.DS.$this->request->action ;

		//model_name
		$controller = ucfirst($this->request->module);
		$this->model_name = '\\'.$controller.'\\Model_'.$controller;

		//nicename 人間向けのモジュール名
		$this->config = \Config::load($controller);
		self::$nicename = $this->config['nicename'];

		//actionset
		\Actionset::forge($this->request->module);




		//parent
		parent::before();

	}

	/**
	 * router()
	*/
	public function router($method, $params)
	{
		$userinfo = \Auth::get_userinfo();

		//ユーザ／ユーザグループ単位のACLを確認する。
		$is_allow = \Auth::auth($this->current_action, $userinfo);

		//権限がなくても、オーナACLがある行為だったらいったん留保
		//modelのauthorized_option()に判断をゆだねる
		if( ! $is_allow):
			$is_allow = \Auth::is_exists_owner_auth($this->request->module, $method);
		endif;

		//存在しないアクション
		if(
			! method_exists(get_called_class(), 'action_'.$method) &&
			! method_exists(get_called_class(), 'get_'.$method)
		){
			$is_allow = false;
			\Session::set_flash('error','"'.htmlspecialchars($method, ENT_QUOTES).'" is not exist.');
		}

		if( ! $is_allow):
			$page = \Request::forge('content/403')->execute();
			return new \Response($page, 403);
		endif;

		//ログイン画面をトップページにする処理
		$use_login_as_top = \Config::get('use_login_as_top');
		if(
			$use_login_as_top && //configで設定
			\Auth::get_user_id() == 0 && //ログイン画面はゲスト用
			$this->request->module.DS.$method == 'content/home' //トップページを求められているとき
		):
			return \Response::redirect(\Uri::create('user/login'));
		endif;



		//通常の処理に渡す

			return parent::router($method, $params);


		// if this is an ajax call
		if ($this->is_restful())
		{
			// have the Controller_Rest router deal with it
			return parent::router($method, $params);
		}

		// check if a input specific method exists
		$controller_method = strtolower(\Input::method()) . '_' . $method;

		// fall back to action_ if no rest method is provided
		if ( ! method_exists($this, $controller_method))
		{
			$controller_method = 'action_'.$method;
		}

		// check if the action method exists
		if (method_exists($this, $controller_method))
		{
			var_dump($controller_method);
			return parent::router($method, $params);
			//return call_fuel_func_array(array($this, $controller_method), $params);
		}

		// if not, we got ourselfs a genuine 404!
		throw new \HttpNotFoundException();




		
	}



	/*
	 * ココからHybridそのまま
	 * 
	 * After controller method has run output the template
	 *
	 * @param  Response  $response
	 */
	public function after($response)
	{
		// return the template if no response is present and this isn't a RESTful call
		if ( ! $this->is_restful())
		{
			// do we have a response passed?
			if ($response === null)
			{
				// maybe one in the rest body?
				$response = $this->response->body;
				if ($response === null)
				{
					// fall back to the defined template
					$response = $this->template;
				}
			}

			if ( ! $response instanceof \Response)
			{
				$response = \Response::forge($response, $this->response_status);
			}
		}

		return parent::after($response);
	}

	/**
	 * Decide whether to return RESTful or templated response
	 * Override in subclass to introduce custom switching logic.
	 *
	 * @param  boolean
	 */
	public function is_restful()
	{
		return \Input::is_ajax();
	}

	public function __get($name) {

		//var_dump($this->_template);

		if ($name == 'template') {
			if (isset($this->template) and $this->template instanceof \View) return $this->template;
			if ( ! empty($this->_template) and is_string($this->_template)) {
				return $this->template = \View::forge($this->_template);
			}

		}


		/*
		// setup the template if this isn't a RESTful call
		if ( ! $this->is_restful())
		{
			if ( ! empty($this->template) and is_string($this->template))
			{
				// Load the template
				$this->template = \View::forge($this->template);
			}
		}
		 */
		// if ($name == 'template') return $this->template = \View::forge('default'); //var_dump($name); die();
	}
}



