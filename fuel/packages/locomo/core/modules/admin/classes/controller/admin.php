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

main_controllerはやめる

'controller_class' => array(
actionset_class....
),

というように。このアクションセットを読んで、モジュールあるいはコントローラのトップページを作る
引数がない婆は、いわゆるダッシュボード。
ダッシュボードに何を出すかは、ユーザが決定する。
モジュールやコントローラはHMVCでブロックを提供するものとする？
提供されるブロックは、action_fooでユーザにひもつけて登録する。
まずはテストモジュールの二つのコントローラについて、configを設定する

*/

		$view = \View::forge('home');
		$view->set_global('title', \Config::get('site_title'));
		$view->base_assign();
		$this->template->content = $view;
	}
}
