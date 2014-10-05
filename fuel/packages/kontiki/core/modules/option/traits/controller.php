<?php
namespace Option;
trait Controller_Option
{
	/**
	 * action_option()
	 */
	public function action_option($optname = null)
	{
		is_null($optname) and die();
		$optinfo = self::$actionset->$optname;
		$items = \Option\Model_Option::find_options($optname);

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
		$view = \View::forge('option_'.$optname);
		$view->set_global('items', $items);
		$view->set_global('title', $optinfo['action_name']);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_option_revisions()
	 */
	public function action_option_revisions($optname = null)
	{
		is_null($optname) and die();
		$model = \Revision\Model_Revision::forge();

		if ( ! $revisions = $model::find_options_revisions($optname)):
			\Session::set_flash('error','履歴がありません');
			return \Response::redirect(\Uri::base());
		endif;

		//view
		$view = \View::forge(\Kontiki\Util::fetch_tpl('/revision/views/index_option.php'));

		$view->set_global('items', $revisions);
		$view->set_global('optname', $optname);
		$view->set_global('title', 'オプションの編集履歴');

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_option_revision()
	 */
	public function action_option_revision($optname = null, $datetime = null)
	{
		if(is_null($optname) || is_null($datetime)) die();
		$model = \Revision\Model_Revision::forge();

		if ( ! $revisions = $model::find_options_revision($optname, $datetime)):
			\Session::set_flash('error','履歴がありません');
			return \Response::redirect(\Uri::base());
		endif;

		//unserialize
		$data = (object) array();
		$data          = (object) unserialize($revisions->data);
		$data->comment = $revisions->comment;

		//view
		$view = \View::forge('option_'.$optname);
		$view->set_global('optname', $optname);
		$view->set_global('items', $data);
		$view->set_global('title', '編集履歴');
		$view->set_global('is_revision', true);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}
}
