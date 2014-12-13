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
		// parent
		parent::before();

		// hmvc
		if (\Request::is_hmvc())
		{
			$this->_template = 'widget';
		}

		// show profile to root only
		\Fuel::$profiling = \Auth::get('id') == -2 ?: false ;

		// template path
		$request = \Request::active();
		$request->add_path(APPPATH.'views'.DS.\Request::main()->module.DS, true);

		// base_url
		$this->base_url = \Uri::create(\Inflector::ctrl_to_dir(\Request::main()->controller)).DS;

		// load config and set model_name
		$controller = substr(ucfirst(\Inflector::denamespace(\Request::main()->controller)), 11);
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
		$called_class = get_called_class();

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

	/*
	 * edit_core()
	 */
	public function edit_core($id = null, $redirect = null)
	{
		// vals
		$model = $this->model_name ;
		$content = \View::forge($this->_content_template ?: 'edit');

		if ($id)
		{
			$obj = $model::find($id, $model::authorized_option(array(), 'edit'));

			// not found
			if ( ! $obj)
			{
				$page = \Request::forge('content/403')->execute();
				return new \Response($page, 403);
			}
			$title = '#' . $id . ' ' . self::$nicename . '編集';
		}
		else
		{
			$obj = $model::forge();
			$title = self::$nicename . '新規作成';
		}
		$form = $model::form_definition('edit', $obj);

		// save
		if (\Input::post())
		{
			if (
				$obj->cascade_set(\Input::post(), $form, $repopulate = true) &&
				 \Security::check_token()
			)
			{
				//save
				if ($obj->save(null, true))
				{
					//success
					\Session::set_flash(
						'success',
						sprintf('%1$sの #%2$d を更新しました', self::$nicename, $obj->id)
					);
					$locomo_path = \Inflector::ctrl_to_dir(\Request::main()->controller.DS.\Request::main()->action);
					$redirect = $redirect ?: $locomo_path.DS.$obj->id;
					return \Response::redirect(\Uri::create($redirect));
				}
				else
				{
					//save failed
					\Session::set_flash(
						'error',
						sprintf('%1$sの #%2$d を更新できませんでした', self::$nicename, $id)
					);
				}
			}
			else
			{
				//edit view or validation failed of CSRF suspected
				if (\Input::method() == 'POST')
				{
					$errors = $form->error();
					// いつか、エラー番号を与えて詳細を説明する。そのときに二重送信でもこのエラーが出ることを忘れず言う。
					if ( ! \Security::check_token()) $errors[] = 'ワンタイムトークンが失効しています。送信し直してみてください。';
					\Session::set_flash('error', $errors);
				}
			}
		}

		//add_actionset - back to index at edit
		$ctrl_url = \Inflector::ctrl_to_dir($this->request->controller);
		$action['urls'][] = \Html::anchor($ctrl_url.DS.'index_admin/','一覧へ');
		$action['order'] = 10;
		\Actionset::add_actionset($this->request->controller, 'ctrl', $action);

		//view
		$this->template->set_global('title', $title);
		$content->set_global('item', $obj, false);
		$content->set_global('form', $form, false);
		$this->template->content = $content;
		$content->base_assign($obj);
	}
}

