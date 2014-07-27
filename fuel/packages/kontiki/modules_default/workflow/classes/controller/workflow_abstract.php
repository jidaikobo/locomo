<?php
namespace Kontiki;
abstract class Controller_Workflow extends \Kontiki\Controller_Crud
{
	/**
	 * set_actionset() - override
	 */
	public function set_actionset($controller = null, $item = null)
	{
		parent::set_actionset();
		require_once(dirname(__DIR__).'/actionset/workflow.php');
		self::$actionset = \Workflow\Actionset_Workflow::actionItems($controller, $item);
	}

	/**
	 * pre_save_hook() - override
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
		if($mode == 'create' && array_key_exists('workflow_actions', self::$actionset)):
			//ワークフロー管理下のコンテンツはbefore_progressで作成される
			$model = $this->model_name ;
			$primary_key = $model::get_primary_key();
			if(isset($obj->$primary_key[0])):
				$obj->workflow_status = 'before_progress';
				$obj->save();
			endif;
		endif;

		return $obj;
	}

	/**
	 * edit_core() - override
	 */
	public function edit_core($id = null, $obj = null, $redirect = null, $title = null)
	{
		if(@$obj->workflow_status == 'in_progress'):
			\Session::set_flash('error','この項目はワークフロー管理下にあり、現在、編集できません。');
			return \Response::redirect(\Uri::create($this->request->module.'/view/'.$id));
		endif;
		//in_progressでない項目であれば、編集できる。
		return parent::edit_core($id, $obj, $redirect, $title);
	}

	/**
	 * action_route()
	 */
	public function action_route($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		//model and view
		$view = \View::forge(\Kontiki\Util::fetch_tpl('/workflow/views/route.php'));
		$model = \Workflow\Model_Workflow::forge();

		//postがあったら経路設定して、表示画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = \Input::post('route');
			if($route_id):
				$model::set_route($route_id, $this->request->module, $id);
				\Session::set_flash('success', 'ルートを設定しました');

				return \Response::redirect(\Uri::create($this->request->module.'/view/'.$id));
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

		//postがあったら申請処理をして、編集画面に戻る
		if (\Input::method() == 'POST'):
			$comment = \Input::post('comment');
			$model::add_log('approve', null, $this->request->module, $id, $comment);
			\Session::set_flash('success', '申請しました');

			//項目のworkflow_statusをin_progressにする（編集できないようにする）
			$target_model = $this->model_name ;
			$obj = $target_model::find_item_anyway($id);
			$obj->workflow_status = 'in_progress';
			$obj->save();

			return \Response::redirect(\Uri::create($this->request->module.'/view/'.$id));
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

		//postがあったら承認処理をして、閲覧画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = $model::get_route($this->request->module, $id);

			if($route_id):
				$comment = \Input::post('comment');
				$model::add_log('approve',$route_id, $this->request->module, $id,$comment);
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
	 * action_remand()
	 */
	public function action_remand($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		$model = \Workflow\Model_Workflow::forge();

		//postがあったら差し戻し処理をして、閲覧画面に戻る
		if (\Input::method() == 'POST'):
			$comment     = \Input::post('comment');
			$target_step = (int) \Input::post('target_step');
			$model::add_log('remand', null, $this->request->module, $id, $comment, $target_step);
			\Session::set_flash('success', '差し戻し処理をしました');

			//差し戻しが最初まで戻った場合、in_progressを解除して編集できるようにする
			if($target_step == -1):
				$target_model = $this->model_name ;
				$obj = $target_model::find_item_anyway($id);
				$obj->workflow_status = 'before_progress';
				$obj->save();
			endif;

			return \Response::redirect(\Uri::create($this->request->module.'/view/'.$id));
		endif;

		//差し戻し候補を取得する
		$current_step = $model::get_current_step($this->request->module, $id);
		$logs         = $model::get_route_logs($this->request->module, $id, $current_step);
		$target_steps = array();

		foreach($logs as $log):
			//初期化と承認のログをとる
			if($log->status == 'remand') continue;

			//現在のステップより上のログは除外する
			if($log->current_step > $current_step) continue;

			//一つ下のステップに設定
			$target_step = (int) $log->current_step - 1;

			//複数回の差戻しによって同じステップ数を持つ場合は、keyで上書きする
			$user_info = \User\Model_User::find_item($log->creator_id);
			$target_steps[$target_step] = $user_info->user_name;
		endforeach;

		//コメント入力viewを表示
		$view = \View::forge(\Kontiki\Util::fetch_tpl('/workflow/views/comment.php'));

		//assign
		$view->set_global('title', '差し戻し');
		$view->set('button',       '差し戻す');
		$view->set('target_steps', $target_steps);
		$view->set('id',           $id);
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
				$model::add_log('reject', $route_id, $this->request->module, $id, $comment);
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
