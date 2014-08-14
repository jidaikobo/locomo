<?php
namespace Kontiki;
abstract class Controller_Options_Abstract extends \Kontiki\Controller
{
	/**
	 * action_options()
	 */
	public function action_options($optname = null)
	{
		is_null($optname) and die();
		$model = $this->model_name ;
		$optinfo = self::$actionset->$optname;

		//view
		if (\Input::method() == 'POST' && \Security::check_token()):
			//新規追加
			if(\Input::post('mode') == 'add'):
				//nameは必須
				if( ! \Input::post('name')):
					$err = array('項目名は必須です');
					\Session::set_flash('error', $err);
				else:
					//add_option()
					$model::add_option($optname, \Input::post());
					\Session::set_flash('success', '項目を新規追加しました');
				endif;
			//編集
			elseif(\Input::post('mode') == 'edit'):
			endif;


/*
			foreach(\Input::post() as $k => $v):
				if($k == 'submit') continue;
				$obj->$k = $v;
			endforeach;
			\Session::set_flash('error', $val->error());
*/
		endif;

		//view
		$view = \View::forge('options_'.$optname);
//		$view->set_global('item', $optinfo['menu_str']);
		$view->set_global('title', $optinfo['menu_str']);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}
}
