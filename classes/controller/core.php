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
		\Profiler::mark('Locomo\\Controller_Core::before() - Called');

		// parent
		parent::before();

		// hmvc
		if (\Request::is_hmvc())
		{
			$this->_template = 'widget';
		}

		// show profile to development only
		\Fuel::$profiling = \Fuel::$env == 'development' ?: false ;
		\Fuel::$profiling = \Input::get('no_prof') ? false : \Fuel::$profiling ;

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
		\Profiler::mark('Locomo\\Controller_Core::router() - Called');

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
				return \Response::redirect(\Uri::create('sys/403'));
			}
			else
			{
				$qstr = \Arr::get($_SERVER, 'QUERY_STRING') ;
				$qstr = $qstr ? '?'.e($qstr) : '' ;
				return \Response::redirect(\Uri::create('auth/login?ret='.\Uri::string().$qstr));
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
				$main_action = \Arr::get($called_class::$locomo, 'main_action');
				$main_action = $main_action ? \Inflector::ctrl_to_dir($called_class.DS.$main_action) : '';
				
				// Locomo is no module
				$main_action = str_replace('locomo/', '' , $main_action);

				if ($main_action)
				{
					return \Response::redirect($main_action);
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
			$page = \Request::forge('sys/403')->execute();
			return new \Response($page, 403);
		}

		// use login as a toppage
		$no_home = \Config::get('no_home');
		if (
			$no_home && // config
			! \Auth::check() && // for guest
			$this->request->controller.DS.$method == 'Controller_Sys/home' // when toppage
		)
		{
			return \Response::redirect(\Uri::create('auth/login'));
		}

		// use dashboard as a loginned toppage
		if (
			$no_home && // config
			\Auth::check() && // for users
			$this->request->controller.DS.$method == 'Controller_Sys/home' // when toppage
		)
		{
			return \Response::redirect(\Uri::create('sys/dashboard'));
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

		$this->base_assign();
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

	/**
	 * base_assign()
	 * @param object $obj use for auth. Fuel\Model object
	 */
	public function base_assign($obj = null)
	{
		// Profiler
		\Profiler::mark('Locomo\\Controller_Core::base_assign() - Called');

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
		$logo_s = APPPATH.'locomo/system/img/logo_s.png';
		if ( ! file_exists($logo_s))
		{
			$logo_s = \Asset::img();
			$logo_s = LOCOMOPATH.'assets/img/system/logo_s.png';
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
		$this->template->set_global('body_class', implode($class_arr, ' '));

		// data-uri
		$this->template->set_global('body_data', 'data-uri='.\Uri::base(false));
		
		// locomo - for logged in users'
		$locomo = array();
		if ( ! \Auth::check())
		{
			$this->template->set_global('locomo', $locomo);
			return;
		}
		
		// locomo path
		$locomo['locomo_path'] = $controller.DS.$action;

		// current controller
		$locomo['controller']['name'] = $controller;
		if (property_exists($controller, 'locomo') && \Arr::get($controller::$locomo, 'main_action'))
		{
			$ctrl_home = \Arr::get($controller::$locomo, 'main_action');
			$locomo['controller']['ctrl_home'] = $ctrl_home;
			$locomo['controller']['home'] = \Uri::create(\Inflector::ctrl_to_dir($controller.DS.$ctrl_home));
			$locomo['controller']['home_name'] = \Arr::get($controller::$locomo, 'main_action_name');
			$locomo['controller']['nicename'] = \Arr::get($controller::$locomo, 'nicename');
		}
		else
		{
			$locomo['controller']['ctrl_home'] = false;
			$locomo['controller']['home'] = false;
			$locomo['controller']['home_name'] = false;
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
				$locomo['module']['ctrl_home'] = $main_action;
				$locomo['module']['home'] = \Uri::create(\Inflector::ctrl_to_dir($main_action));
				$locomo['module']['nicename'] = $config['nicename'];
				$locomo['module']['main_controller'] = $config['main_controller'];
			}
			else
			{
				$locomo['module']['ctrl_home'] = false;
				$locomo['module']['home'] = false;
				$locomo['module']['nicename'] = $module;
				$locomo['module']['main_controller'] = false;
			}
		}

		// get accessible controller
		\Profiler::mark('Locomo\\Controller_Core::base_assign() - get accessible controller');
		$all_ctrls = \Util::get_mod_or_ctrl();
		foreach($all_ctrls as $k => $v)
		{
			if ( ! \Auth::instance()->has_access(\Arr::get($v, 'admin_home')))
			{
				unset($all_ctrls[$k]);
			}
		}
		$locomo['controllers'] = $all_ctrls;

		$this->template->set_global('locomo', $locomo);

		// actionset
		\Profiler::mark('Locomo\\Controller_Core::base_assign() - actionset');
		$default = ['index' => [], 'base' => [], 'ctrl' => []];
		$actionset = \Actionset::get_actionset($controller, static::$obj, $default);
		$this->template->set_global('actionset', $actionset, false);

		// Profiler
		\Profiler::mark('Locomo\\Controller_Core::base_assign() - done');
	}

	protected static $obj = null;
	public static function set_object($obj) {
		static::$obj = $obj;
	}
}

