<?php
namespace Locomo;
class Controller_Core extends \Fuel\Core\Controller_Rest
{
	/**
	* @var string name for human
	*/
	public static $nicename = '';

	/**
	* @var string template
	*/
	public $_template = 'admin';

	/*
	 * @var string model name
	 */
	protected $model_name = '';

	/*
	 * @var string base_url
	 */
	protected $base_url = '';

	/**
	 * @var string config
	 */
	protected static $config = '';

	/**
	 * before()
	 */
	public function before()
	{
		// Profiler
		\Profiler::mark('Locomo\\Controller_Base::before() - Called');

		// parent
		parent::before();

		// hmvc
		if (\Request::is_hmvc())
		{
			$this->_template = 'widget';
		}

		// show profile to user_id == 1 only
		\Fuel::$profiling = \Auth::get('id') == 1 ?: false ;

		// template path
		$request = \Request::active();
		$request->add_path(APPPATH.'views'.DS.\Request::main()->module.DS, true);

		// base_url
		$this->base_url = \Uri::create(\Inflector::ctrl_to_dir(\Request::main()->controller)).DS;

		// load config and set model_name
		$controller = substr(ucfirst(\Inflector::denamespace(\Request::active()->controller)), 11);
		$current_module = \Request::main()->module;
		if (\Request::is_hmvc())
		{
			$current_module = \Request::active()->module;
		}
		if ($current_module)
		{
			$module = ucfirst($current_module);
//			if (! $this->model_name) $this->model_name = '\\'.$module.'\\Model_'.$module;
			if (! $this->model_name) $this->model_name = '\\'.$module.'\\Model_'.$controller;
			static::$config = \Config::load(strtolower($this->request->module));
		}
		else
		{
			if (! $this->model_name) $this->model_name = '\\Model_'.$controller;
			static::$config = \Config::load(strtolower($controller));
		}
		static::$config = static::$config ?: array();

		// nicename
		$called_class = get_called_class();
		$nicename = '';
		if (property_exists($called_class, 'locomo'))
		{
			$nicename = \Arr::get($called_class::$locomo, 'nicename');
		}
		$nicename = $nicename ?: @static::$config['nicename'];
		static::$nicename = $nicename;
	}

	/**
	 * router()
	*/
	public function router($method, $params)
	{
		// Profiler
		\Profiler::mark('Locomo\\Controller_Base::router() - Called');

		// fetch_view() can be executed without acl
		if (
			\Request::main()->controller == 'Content\\Controller_Content' &&
			$method == 'fetch_view'
		)
		{
			return parent::router($method, $params);
		}

		// auth
		if ( ! static::auth())
		{
			if (\Auth::check())
			{
				return \Response::redirect(\Uri::create('content/content/403'));
			}
			else
			{
				$qstr = \Arr::get($_SERVER, 'QUERY_STRING') ;
				$qstr = $qstr ? '?'.e($qstr) : '' ;
				return \Response::redirect(\Uri::create('user/auth/login?ret='.\Uri::string().$qstr));
			}
		}

		// action not exists - index
		$called_class = get_called_class();
		$is_allow = true;
		if (
			! method_exists($called_class, 'action_index') &&
			\Request::main()->action == 'index'
		)
		{
			if (property_exists($called_class, 'locomo'))
			{
				$admin_home = \Arr::get($called_class::$locomo, 'admin_home');
				$admin_home = $admin_home ? \Inflector::ctrl_to_dir($admin_home) : '';
				if ($admin_home)
				{
					return \Response::redirect($admin_home);
				}
			}
		}

		// action not exists
		$is_allow = true;
		if (
			! method_exists($called_class, 'action_'.$method) &&
			! method_exists($called_class, 'get_'.$method)
		)
		{
			$is_allow = false;
			\Session::set_flash('error','"'.htmlspecialchars($method, ENT_QUOTES).'" is not exist.');
		}

		if ( ! $is_allow)
		{
			$page = \Request::forge('content/content/403')->execute();
			return new \Response($page, 403);
		}

		// use login as a toppage
		$no_home = \Config::get('no_home');
		if (
			$no_home && // config
			! \Auth::check() && // for guest
			$this->request->module.DS.$method == 'content/home' // when toppage
		)
		{
			return \Response::redirect(\Uri::create('user/auth/login'));
		}

		// use dashboard as a loginned toppage
		if (
			$no_home && // config
			\Auth::check() && // for users
			$this->request->module.DS.$method == 'content/home' // when toppage
		)
		{
			return \Response::redirect(\Uri::create('admin/admin/dashboard'));
		}

		return parent::router($method, $params);
	}

	/**
	 * auth()
	 * @return void
	 */
	public function auth()
	{
		$current_action = '\\'.$this->request->controller.DS.$this->request->action.DS;

		// ordinary auth
		$is_allow = \Auth::instance()->has_access($current_action);

		// additional conditions
		$conditions = \Arr::get(static::$config, 'conditioned_allowed', false);
		if ( ! $is_allow && $conditions && array_key_exists($current_action, $conditions))
		{
			$methods = $conditions[$current_action];
			if ( ! method_exists($methods[0], $methods[1])) throw new \BadFunctionCallException();
			$is_allow = $methods[0]::$methods[1]();
		}

		return $is_allow;
	}

	/*
	 * ココからHybridそのまま
	 * After controller method has run output the template
	 * @param  Response  $response
	 */
	public function after($response)
	{
		if (!isset($this->template->title)) throw new \Exception("template に title を設定して下さい。<br>\$this->template->set_global('title', TITLE_VALUE')");

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

	/*
	 * __get()
	 */
	public function __get($name)
	{
		//var_dump($this->_template);

		if ($name == 'template')
		{
			if (isset($this->template) and $this->template instanceof \View) return $this->template;
			if ( ! empty($this->_template) and is_string($this->_template))
			{
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

