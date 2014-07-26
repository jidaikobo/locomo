<?php
namespace Kontiki;
abstract class Controller_Workflow extends \Kontiki\Controller_Crud
{
	/**
	 * pre_save_hook()
	 */
	public function pre_save_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);
		$obj = parent::pre_save_hook($obj, $mode);

		//ワークフロー管理
		if(array_key_exists('workflow_actions', self::$actionset)):
			//ワークフロー管理するコントローラにはworkflow_statusを作る
			$model = $this->model_name ;
			if( ! \DBUtil::field_exists($model::get_table_name(), array('workflow_status'))):
				\DBUtil::add_fields($model::get_table_name(),array(
					'workflow_status' => array('constraint' => 50, 'type' => 'varchar', 'null' => true),
				));
				\Session::set_flash('success', 'ワークフロー管理をするために、workflow_statusフィールドを追加しました。当該モジュールのモデルの$_propertiesにworkflow_statusを足してください。');
			endif;
		endif;

		return $obj;
	}

	/**
	 * post_save_hook()
	 */
	public function post_save_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);
		$obj = parent::post_save_hook($obj, $mode);

		//ワークフロー管理
		if(array_key_exists('workflow_actions', self::$actionset)):
			//承認段階を確認し、最終承認がまだであれば常にworkflow_statusをin_progressにする
			$model = $this->model_name ;
			$primary_key = $model::get_primary_key();
			if(isset($obj->$primary_key[0])):
				if(\Workflow\Model_Workflow::is_in_workflow($this->request->module, $obj->$primary_key[0])):
					$obj->workflow_status = 'in_progress';
					$obj->save();
				endif;
			endif;
		endif;

		return $obj;
	}

	/**
	 * action_route()
	 */
	public function action_route($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());


		//model and view
		$view = \View::forge(\Kontiki\Util::fetch_tpl('/workflow/views/route.php'));
		$model = \Workflow\Model_Workflow::forge();

		//postがあったら経路設定して、編集画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = \Input::post('route');
			if($route_id):
				$model::set_route($route_id, $this->request->module, $id);
				\Session::set_flash('success', 'ルートを設定しました');
				return \Response::redirect(\Uri::create($this->request->module.'/edit/'.$id));
			else:
				\Session::set_flash('error', 'ルートを選択してください');
			endif;
		endif;

		//設定されている経路をすべて取得
		$items = $model->find_items();

		//現在設定されている経路を取得
		$route_id = $model::get_route($this->request->module, $id);

		//assign
		$view->set_global('title', 'ルート設定');
		$view->set('button', '申請する');
		$view->set('items', $items);
		$view->set('route_id', $route_id);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}


	/**
	 * action_apply()
	 */
	public function action_apply($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		$model = \Workflow\Model_Workflow::forge();

		//postがあったら経路設定して、編集画面に戻る
		if (\Input::method() == 'POST'):
			$comment = \Input::post('comment');
			$model::add_log('increase', null, $this->request->module, $id, $comment);
			\Session::set_flash('success', '申請しました');
			return \Response::redirect(\Uri::create($this->request->module.'/edit/'.$id));
		endif;

		//コメント入力viewを表示
		$view = \View::forge(\Kontiki\Util::fetch_tpl('/workflow/views/comment.php'));

		//assign
		$view->set_global('title', '承認申請');
		$view->set('button', '申請する');
		$view->set('id', $id);
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_approve()
	 */
	public function action_approve($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		//model and view
		$view = \View::forge(\Kontiki\Util::fetch_tpl('/workflow/views/comment.php'));
		$model = \Workflow\Model_Workflow::forge();

		//postがあったら経路設定して、編集画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = $model::get_route($this->request->module, $id);

			if($route_id):
				$comment = \Input::post('comment');
				$model::add_log($mode = 'increase',$route_id, $this->request->module, $id,$comment);
				\Session::set_flash('success', '承認しました');
				return \Response::redirect(\Uri::create($this->request->module.'/view/'.$id));
			endif;
		endif;

		//assign
		$view->set_global('title', '承認');
		$view->set('button', '承認する');
		$view->set('id', $id);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_reject()
	 */
	public function action_reject($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		//model and view
		$view = \View::forge(\Kontiki\Util::fetch_tpl('/workflow/views/comment.php'));
		$model = \Workflow\Model_Workflow::forge();

		//postがあったら経路設定して、編集画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = $model::get_route($this->request->module, $id);

			if($route_id):
				$comment = \Input::post('comment');
				$model::add_log($mode = 'reject',$route_id, $this->request->module, $id,$comment);
				\Session::set_flash('success', '項目を却下しました');

				//項目を削除する（可能であればソフトデリートする）
				$target_model = $this->model_name ;
				$obj = $target_model::find_item_anyway($id);
				$target_model::delete_item($obj);
				return \Response::redirect(\Uri::create($this->request->module));
			endif;
		endif;

		//assign
		$view->set_global('title', '却下の確認');
		$view->set('button', '却下する');
		$view->set('id', $id);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

}
