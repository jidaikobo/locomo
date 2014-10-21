<?php
namespace Locomo_Core_Module\Content;
class Controller_Content extends \Locomo\Controller_Crud
{
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
		if(substr(\Uri::string(),0,12) == 'content/home'):
			return \Response::redirect('/', 'location', 404);
		endif;

		//描画
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
		$view->set_global('title', 'Page Not Found');
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	* action_403()
	* 403
	*/
	public function action_403()
	{
		$view = \View::forge('403');
		$view->set_global('title', 'Forbidden');
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	* action_help_index()
	*/
	public function action_help_index()
	{
		


		//描画
		$view = \View::forge('home');
		$view->set_global('title', \Config::get('site_title'));
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	* action_help()
	*/
	public function action_help($filename)
	{
		


		//描画
		$view = \View::forge('home');
		$view->set_global('title', \Config::get('site_title'));
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}


	/**
	 * action_fetch_view()
	 * fetch files view
	 */
	public function action_fetch_view()
	{
		//ヘンなアクセスを追い返す
		$ext = \Input::extension();
		$args = func_get_args();
		$path = join('/', $args).'.'.$ext;
		if(empty($path) || empty($ext))
			return \Response::redirect('/', 'location', 404);

		//存在確認
		$filename = PKGCOREPATH."view/{$path}";
		if( ! file_exists($filename)) return \Response::forge();

		//projectごとに違うコンフィグを読む工夫は後で考える
		$config = \Config::load(PKGCOREPATH.'/config/upload.php');
		$ext = strtolower($ext);
		if( ! isset($config['mime_whitelist'][$ext])) return \Response::forge();

		//profilerをoffに
		\Fuel\Core\Fuel::$profiling = false;

		//描画
		$headers = array( 'Content-type' => $config['mime_whitelist'][$ext] );
		$view = \View::forge('fetch_view');
		$view->set_global('item', file_get_contents($filename), false);
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view),200, $headers);
	}
}
