<?php
namespace Locomo;
class Controller_Core extends \Fuel\Core\Controller_Rest
{
	/**
	* @vars
	*/
	public static $nicename    = ''; // name for human
	public static $controller  = ''; // \Request::main()->controller
	public static $action      = ''; // \Request::main()->action
	public static $shortname   = ''; // '[\Modname]\Controller_Example' to 'example'
	public static $base_url    = ''; // http(s)://example.com/path/to/controller
	public static $main_url    = ''; // http(s)://example.com/path/to/controller/main_action
	public static $current_url = ''; // http(s)://example.com/path/to/controller/current_action

	/**
	* @var string template
	*/
	public $_template = 'admin';

	/*
	 * @var string model name
	 */
	protected $model_name = '';

	/*
	 * @var string redirect
	 */
	protected static $redirect = '';

	/**
	 * @var string config
	 */
	protected static $config = array();

	/**
	 * before()
	 */
	public function before()
	{
		// Profiler
		\Profiler::mark('Locomo\\Controller_Core::before() - Called');

		// parent
		parent::before();

		// show profile to development only
		\Fuel::$profiling = \Fuel::$env == 'development' ?: false ;
		\Fuel::$profiling = \Input::get('no_prof') ? false : \Fuel::$profiling ;
		\Fuel::$profiling = \Locomo\Browser::getIEVersion() && \Locomo\Browser::getIEVersion() <= 8 ? false : \Fuel::$profiling;

		// hmvc
		$this->_template = \Request::is_hmvc() ? 'widget' : $this->_template ;

		// called_class
		$called_class = get_called_class();
		static::$controller  = \Inflector::add_head_backslash(\Request::main()->controller);
		static::$action      = \Request::main()->action;
		static::$base_url    = \Uri::create(\Inflector::ctrl_to_dir(\Request::main()->controller)).DS;
		static::$base_url    = str_replace('locomo/', '' , static::$base_url); // locomo is not module
		static::$current_url = static::$base_url.\Request::main()->action.DS;
		$main_action = '/'.\Util::get_locomo($called_class, 'main_action');
		static::$main_url    = \Uri::create(\Inflector::ctrl_to_dir(\Request::main()->controller.$main_action)) ;
		static::$nicename    = \Util::get_locomo($called_class, 'nicename') ?: @static::$config['nicename'];
		static::$shortname   = strtolower(substr(\Inflector::denamespace(\Request::active()->controller), 11));

		// load config and set model_name
		$module = \Request::is_hmvc() ? \Request::active()->module : \Request::main()->module;
		if ($module)
		{
			$this->model_name = $this->model_name ?: '\\'.ucfirst($module).'\\Model_'.\Inflector::words_to_upper(static::$shortname);
			static::$config = \Config::load(strtolower($module), true);
		}
		else
		{
			$this->model_name = $this->model_name ?: '\\Model_'.\Inflector::words_to_upper(static::$shortname);
			static::$config = \Config::load(static::$shortname, true);
		}
		static::$config = static::$config ?: array();
	}

	/**
	 * router()
	*/
	public function router($method, $params)
	{
		// Profiler
		\Profiler::mark('Locomo\\Controller_Core::router() - Called');

		// action not exists - got to main_url instead of index
		$called_class = get_called_class();
		$is_allow = true;
		if (
			! method_exists($called_class, 'action_index') &&
			\Request::main()->action == 'index'
		)
		{
			return \Response::redirect(static::$main_url);
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
		$current_action = '\\'.\Request::active()->controller.'/'.$this->request->action;

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
		// redirect
		if(static::$redirect) \Response::redirect(\Uri::create(static::$redirect));

		// error
		if (!isset($this->template->title)) throw new \Exception("template に title を設定して下さい。\$this->template->set_global('title', 'TITLE_VALUE');");

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

		// base assign
		$this->base_assign();

		// event
		if (\Event::instance()->has_events('locomo_after'))
		{
			$response = \Event::instance()->trigger('locomo_after', (string) $response);
			$response = \Response::forge($response, $this->response_status);
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

	/**
	 * base_assign()
	 * @param object $obj use for auth. Fuel\Model object
	 */
	public function base_assign()
	{
		// Profiler
		\Profiler::mark('Locomo\\Controller_Core::base_assign() - Called');

		// module and controller
		$module     = \Inflector::remove_head_backslash(\Request::main()->module);
		$controller = \Inflector::add_head_backslash(\Request::main()->controller);
		$action     = \Request::main()->action;

		// custom icon and background
		$idty_class = '';
		$idty_class.=  ! file_exists(APPPATH.'locomo/assets/img/system/logo.png') ? ' default_logo' : '';
		$idty_class.=  ! file_exists(APPPATH.'locomo/assets/img/system/logo_s.png') ? ' default_logo_s' : '';
		$idty_class.=  ! file_exists(APPPATH.'locomo/assets/img/system/adminbar_bg.png') ? ' default_bg' : '';

		// body_class
		$class_arr = array(
			'lcm_module_'.strtolower($module),
			'lcm_ctrl_'.strtolower(\Inflector::ctrl_to_safestr($controller)),
			'lcm_action_'.strtolower($action),
			'lcm_browser_'.\Locomo\Browser::getBrowserType(),
			'lcm_ieversion_'.\Locomo\Browser::getIEVersion(),
		);
		if ($action == 'login' && \Config::get('no_home'))
		{
			$class_arr[] = 'home';
		}
		if (\Auth::check())
		{
			$class_arr[] = 'loggedin';
		}
		$this->template->set_global('body_class', implode($class_arr, ' ').$idty_class);

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

		// current model
		$locomo['model'] = $this->model_name;

		// ua
//		$locomo['ua']['browser'] = \Agent::browser();
//		$locomo['ua']['version'] = \Agent::version();
		
		// current controller
		$locomo['controller']['name'] = $controller;
		if (property_exists($controller, 'locomo') && \Arr::get($controller::$locomo, 'main_action'))
		{
			$main_action = \Arr::get($controller::$locomo, 'main_action');
			$locomo['controller']['main_action'] = $main_action;
			$locomo['controller']['main_url'] = \Uri::create(\Inflector::ctrl_to_dir($controller.DS.$main_action));
			$locomo['controller']['nicename'] = \Arr::get($controller::$locomo, 'nicename');
		}
		else
		{
			$locomo['controller']['main_action'] = false;
			$locomo['controller']['main_url'] = false;
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
				$locomo['module']['main_action'] = $main_action;
				$locomo['module']['main'] = \Uri::create(\Inflector::ctrl_to_dir($main_action));
				$locomo['module']['nicename'] = $config['nicename'];
				$locomo['module']['main_controller'] = $config['main_controller'];
			}
			else
			{
				$locomo['module']['main_action'] = false;
				$locomo['module']['main'] = false;
				$locomo['module']['nicename'] = $module;
				$locomo['module']['main_controller'] = false;
			}
		}

		// get accessible controller
		\Profiler::mark('Locomo\\Controller_Core::base_assign() - get accessible controller');
		$all_ctrls = \Util::get_mod_or_ctrl();
		foreach($all_ctrls as $k => $v)
		{
			if ( ! \Auth::has_access(\Arr::get($v, 'main_action')))
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
		$actionset['index'] = \Arr::get($actionset, 'index', array());
		$this->template->set_global('actionset', $actionset, false);

		// Profiler
		\Profiler::mark('Locomo\\Controller_Core::base_assign() - done');
	}

	/**
	 * set_object
	 */
	protected static $obj = null;
	public static function set_object($obj) {
		static::$obj = $obj;
	}
}

