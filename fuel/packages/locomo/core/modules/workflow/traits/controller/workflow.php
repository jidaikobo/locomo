<?php
namespace Workflow;
trait Controller_Workflow
{
	/**
	 * pre_workflow_save_hook()
	 */
	public function pre_workflow_save_hook($obj = null, $mode = 'edit')
	{
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
	 * workflow_save_hook()
	 */
/*
	public function workflow_save_hook($obj = null, $mode = 'edit')
	{
		//ワークフロー管理
		if($mode == 'create' && array_key_exists('workflow_actions', self::$actionset)):
			//ワークフロー管理下のコンテンツのworkflow_statusはbefore_progressで作成される
			$model = $this->model_name ;
			$primary_key = $model::get_primary_key();
			if(isset($obj->$primary_key[0])):
				$obj->workflow_status = 'before_progress';
				$obj->save();
			endif;
		endif;

		return $obj;
	}
*/

	/**
	 * workflow_edit_core()
	 */
	public function workflow_edit_core($id = null, $obj = null, $redirect = null, $title = null)
	{
		if(@$obj->workflow_status == 'in_progress'):
			\Session::set_flash('error','この項目はワークフロー管理下にあり、現在、編集できません。');
			return \Response::redirect(\Uri::create($this->request->module.'/view/'.$id));
		endif;
		//in_progressでない項目であれば、編集できる。
		return parent::edit_core($id, $obj, $redirect, $title);
	}

	/**
	 * action_index_workflow()
	 */
	public function action_index_workflow($pagenum = null)
	{
		//model and view
		$view = \View::forge(\Locomo\Util::fetch_tpl('/workflow/views/index_workflow.php'));
		$model = \Workflow\Model_Workflow::forge();

		//ユーザが関わっている項目すべて
		$current_items   = $model->get_related_current_items();

		//ユーザが行動しなければならない項目のみ
		$available_items = $model->get_related_current_available_items();

		//比較用配列
		$cmp_arr = array();
		foreach($available_items as $available_item):
			$cmp_arr[] = $available_item->controller.'::'.$available_item->controller_id;
		endforeach;

		//一覧用にマージ
		$items = array();
		foreach($current_items as $k => $current_item):
			//存在比較用文字列
			$cmp_str = $current_item->controller.'::'.$current_item->controller_id;

			//一覧用配列を作る
			$items[$k]['controller']    = $current_item->controller;
			$items[$k]['controller_id'] = $current_item->controller_id;
			$items[$k]['is_current']    = in_array($cmp_str, $cmp_arr) ? true : false;
			$items[$k]['item']          = $model::find_item_by_ctrl_and_id($current_item->controller, $current_item->controller_id);

			//表示用の名称
			$modelname = \Locomo\Util::get_valid_model_name($current_item->controller);
			$items[$k]['primary_name_field'] = $modelname::get_primary_name();
		endforeach;

		//assign
		$view->set_global('title', '関連ワークフロー');
		$view->set('items', \Arr::sort($items, 'is_current', 'desc'));

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_route()
	 */
	public function action_route($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		//model and view
		$view = \View::forge(\Locomo\Util::fetch_tpl('/workflow/views/route.php'));
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

		//現在設定されている経路を取得（将来のルート変更用）
		$route_id = $model::get_route($this->request->module, $id);

		//assign
		$view->set_global('title', 'ルート設定');
		$view->set('button', '申請する');
		$view->set('items', $items);
		$view->set('route_id', $route_id);
		$view->set('item_id', $id);

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
		$view = \View::forge(\Locomo\Util::fetch_tpl('/workflow/views/comment.php'));

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
		$view = \View::forge(\Locomo\Util::fetch_tpl('/workflow/views/comment.php'));
		$model = \Workflow\Model_Workflow::forge();

		//postがあったら承認処理をして、閲覧画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = $model::get_route($this->request->module, $id);
			if($route_id):
				$comment = \Input::post('comment');

				//最後の承認かどうか確認する
				$current_step = $model::get_current_step($this->request->module, $id) + 1;
				$total_step   = $model::get_total_step($route_id);
				$mode = $current_step == $total_step ? 'finish' : 'approve';

				//add_log
				$model::add_log($mode, $route_id, $this->request->module, $id,$comment);

				//最後の承認であれば、項目のステータスを変更する
				if($mode == 'finish'):
					$target_model = $this->model_name ;
					$obj = $target_model::find_item_anyway($id);
					$obj->workflow_status = 'finish';
					$obj->save();
					\Session::set_flash('success', '最終の承認をしました');
				else:
					\Session::set_flash('success', '承認しました');
				endif;

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
		$view = \View::forge(\Locomo\Util::fetch_tpl('/workflow/views/comment.php'));

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
		$view = \View::forge(\Locomo\Util::fetch_tpl('/workflow/views/comment.php'));
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
