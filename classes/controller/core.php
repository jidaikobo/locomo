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
	public static $dir         = ''; // '[\Modname]\Controller_Exp_Example' to 'exp/example/'
	public static $base_url    = ''; // http(s)://example.com/path/to/controller
	public static $main_url    = ''; // http(s)://example.com/path/to/controller/main_action
	public static $current_url = ''; // http(s)://example.com/path/to/controller/current_action/

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
		\Fuel::$profiling = file_exists(APPPATH.'noprof') ? false : \Fuel::$profiling;

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
		static::$dir         = str_replace('_', DS, static::$shortname).DS;

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

		// uri_segment
		if (is_numeric(\Pagination::get('uri_segment')))
		{
			$suspicious_segment = \Arr::search(\Uri::segments(), \Request::main()->action) + 2;
			\Pagination::set('uri_segment', $suspicious_segment);
		}
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
			! method_exists($called_class, 'post_'.$method) &&
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
	public static function auth()
	{
		// Do not use 'DS' instead of '/' for windows environment!!
		$current_action = '\\'.\Request::active()->controller.'/'.\Request::active()->action;

		// ordinary auth
		$is_allow = \Auth::instance()->has_access($current_action);

		// ログイン画面へのip制限
		if (\Config::get('allowed_ip_access_admin') &&
				$current_action == '\Controller_Auth/login' &&
				$_SERVER['REMOTE_ADDR'] != \Config::get('allowed_ip_access_admin'))
		{
			return \Response::redirect('/');
		}

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

		// title
		if ( ! isset($this->template->title)) $this->template->set_global('title', '');

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

		// base assign - header
		\Profiler::mark('Locomo\\Controller_Core::forge header - Called');
		$tpl = $this->_template == 'admin' ? 'header/admin' : 'header';
		$header = \Presenter::forge($tpl);
		$this->template->set_safe('header', $header);
		\Profiler::mark('Locomo\\Controller_Core::forge header - done');

		// footer
		$tpl = $this->_template == 'admin' ? 'footer/admin' : 'footer';
		$this->template->set_safe('footer', \Presenter::forge($tpl));

		// event
		if (\Event::instance()->has_events('locomo_after'))
		{
			$response = \Event::instance()->trigger('locomo_after', (string) $response);
			$response = \Response::forge($response, $this->response_status);
		}

		return parent::after($response);
	}

	/**
	 * set_object
	 */
	protected static $obj = null;
	public static function set_object($obj) {
		static::$obj = $obj;
	}

	/**
	 * get_object
	 */
	public static function get_object() {
		return static::$obj;
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
