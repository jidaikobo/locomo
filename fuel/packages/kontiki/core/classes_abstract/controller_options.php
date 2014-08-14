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
		$items = $model::find_options($optname);

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
			//削除
			elseif(\Input::post('delete')):
				$id = key(\Input::post('delete'));
				$model::delete_option($optname, $id);
				\Session::set_flash('success', '項目を削除しました');
			//編集
			elseif(\Input::post('mode') == 'edit'):
				$err = array();
				foreach(\Input::post('items') as $item):
					if(empty($item['name'])):
						$err = array('項目名が空の値については更新できませんでした');
					else:
						$model::update_option($optname, $item);
					endif;
				endforeach;
				//編集のメッセージ
				if($err):
					\Session::set_flash('error', $err);
				else:
					\Session::set_flash('success', '項目を編集しました');
				endif;
			endif;
			//編集あるいは新規追加が終わったらリビジョンをアップデート
			$items = $model::find_options($optname);
			$args = array();
			$args['controller']    = $optname;
			$args['controller_id'] = 0;
			$args['data']          = serialize($items);
			$args['comment']       = \Input::post('revision_comment') ?: '';
			$args['created_at']    = date('Y-m-d H:i:s');
			$args['modifier_id']   = \User\Controller_User::$userinfo['user_id'];
			$rev_model = \Revision\Model_Revision::forge($args);
			$rev_model->insert_revision();
		endif;

		//view
		$view = \View::forge('options_'.$optname);
		$view->set_global('items', $items);
		$view->set_global('title', $optinfo['menu_str']);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}
}
