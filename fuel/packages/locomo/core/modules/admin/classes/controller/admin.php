<?php
namespace Admin;
class Controller_Admin extends \Locomo\Controller_Base
{
	/**
	* action_home()
	* toppgae
	*/
	public function action_home($mod_or_ctrl = '')
	{
	


/*
//引数がモジュールかコントローラだったら、そのconfigを読む
//configは、1モジュール（1クラス）ひとつ。モジュールの場合だったら、configには複数のコントローラのactionsetクラスなどが定義されているはず。

というように。このアクションセットを読んで、モジュールあるいはコントローラのトップページを作る
引数がない婆は、いわゆるダッシュボード。

*/

		$view = \View::forge('home');
		$view->set_global('title', \Config::get('site_title'));
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	* action_dashboard()
	* dashboard
	*/
	public function action_dashboard()
	{
/*
メニューの一番上はダッシュボード




ログイン後、リダイレクト先がないときには、ここに来る。
ダッシュボードに何を出すかは、個々のユーザが決定するとよいとおもう。
モジュールやコントローラはHMVCでブロックを提供するものとする？
提供されるブロックは、action_fooでユーザにひもつけて登録する。
まずはテストモジュールの二つのコントローラについて、configを設定する
*/
		$view = \View::forge('dashboard');
		$view->set_global('title', \Config::get('site_title'));
		$view->base_assign();
		$this->template->content = $view;
	}
}
