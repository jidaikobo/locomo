<?php
namespace Content;
class Controller_Content extends \Locomo\Controller_Base
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => false,
		'order_at_menu' => 1000,
		'is_for_admin' => false,
		'nicename' => 'ホーム',
		'admin_home' => '\\Content\\Controller_Content/home',
	);

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
		//このアクションはトップページ専用として、content/homeへのアクセスはできないようにする。
		if (substr(\Uri::string(),0,12) == 'content/home'):
			return \Response::redirect('/', 'location', 404);
		endif;

		//描画
		$view = \View::forge('home');
		$view->set_global('title', \Config::get('slogan'));
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	* action_404()
	* 404
	*/
	public function action_404()
	{
		$this->_template = 'default';
		$view = \View::forge('404');
		$view->set_global('title', 'Not Found');
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	* action_403()
	* 403
	*/
	public function action_403()
	{
		$view = \View::forge('403');
		$view->set_global('title', 'Forbidden');
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	* action_help_index()
	*/
	public function action_help_index()
	{
		//描画
		$view = \View::forge('home');
		$view->set_global('title', \Config::get('site_title'));
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	* action_help()
	*/
	public function action_help($filename)
	{
		//描画
		$view = \View::forge('home');
		$view->set_global('title', \Config::get('site_title'));
		$view->base_assign();
		$this->template->content = $view;
	}


	/**
	 * action_fetch_view()
	 * fetch files view
	 */
	public function action_fetch_view()
	{
		// echo 0 ; die(0);
		//ヘンなアクセスを追い返す
		$ext = \Input::extension();
		$args = func_get_args();
		$path = join('/', $args).'.'.$ext;
		if (empty($path) || empty($ext))
			return \Response::redirect('/', 'location', 404);

		//存在確認
		$filename = '' ;
		$locomo_assets = LOCOMOPATH."assets/{$path}";
		$app_assets = APPPATH."locomo/assets/{$path}";

		$filename = file_exists($locomo_assets) ? $locomo_assets : '';
		$filename = file_exists($app_assets)    ? $app_assets : $filename;

		if ( ! $filename)
		{
			$page = \Request::forge('content/content/404')->execute();
			return new \Response($page, 404);
		}

		//拡張子を確認
		$config = \Config::load('upload');
		$ext = strtolower($ext);
		if ( ! isset($config['mime_whitelist'][$ext])) return \Response::forge();

		//profilerをoffに
		\Fuel\Core\Fuel::$profiling = false;

		//描画
		$headers = array( 'Content-type' => $config['mime_whitelist'][$ext] );
		$this->template->set_global('title', '');
		return \Response::forge(file_get_contents($filename), 200, $headers);
	}
}
