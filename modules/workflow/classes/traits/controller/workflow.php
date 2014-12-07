<?php
namespace Workflow;
trait Traits_Controller_Workflow
{
	/**
	 * action_index_workflow()
	 */
	public function action_index_workflow($pagenum = null)
	{
		//model and view
		$view = \View::forge('index_workflow');
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

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

/*
ここでしか使ってないので、移動
	public static function get_valid_model_name($controller = null)
	{
		is_null($controller) and \Response::redirect(\Uri::base());
		$controller_ucfirst = ucfirst($controller);
		return "\\$controller_ucfirst\Model_".$controller_ucfirst;
	}
*/
			$modelname = \Util::get_valid_model_name($current_item->controller);
die('プライマリネームはやめたので、propertiesをみるようにする');
			$items[$k]['primary_name_field'] = $modelname::get_primary_name();
		endforeach;

		//assign
		$view->set_global('title', '関連ワークフロー');
		$view->set('items', \Arr::sort($items, 'is_current', 'desc'));
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_route()
	 */
	public function action_route($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		//model and view
		$view = \View::forge(LOCOMOPATH.'modules/workflow/views/route.php');
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		//postがあったら経路設定して、表示画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = \Input::post('route');
			if ($route_id):
				$model::set_route($route_id, $this->request->controller, $id);
				\Session::set_flash('success', 'ルートを設定しました');

				return \Response::redirect(\Uri::create(\Inflector::ctrl_to_dir($this->request->controller.'/view/'.$id)));
			else:
				\Session::set_flash('error', 'ルートを選択してください');
			endif;
		endif;

		//設定されている経路をすべて取得
		$model_wfadmin = \Workflow\Model_Workflowadmin::forge();
		$items = $model_wfadmin->find('all');

		//現在設定されている経路を取得（将来のルート変更用）
		$route_id = $model::get_route($this->request->controller, $id);

		//add_actionset - back to edit
		$ctrl_url = \Inflector::ctrl_to_dir($this->request->controller);
		$action['urls'][] = \Html::anchor($ctrl_url.DS.'edit/'.$id,'戻る');
		$action['order'] = 10;
		\Actionset::add_actionset($this->request->controller, 'ctrl', $action);

		//assign
		$view->set_global('title', 'ルート設定');
		$view->set('button', '申請する');
		$view->set('items', $items);
		$view->set('route_id', $route_id);
		$view->set('item_id', $id);
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_apply()
	 */
	public function action_apply($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		//postがあったら申請処理をして、編集画面に戻る
		if (\Input::method() == 'POST'):
			$comment = \Input::post('comment');
			$model::add_log('approve', null, $this->request->controller, $id, $comment);
			\Session::set_flash('success', '申請しました');

			//項目のworkflow_statusをin_progressにする（編集できないようにする）
			$target_model = $this->model_name ;
			$obj = $target_model::find($id);
			$obj->workflow_status = 'in_progress';
			$obj->save();

			return \Response::redirect(\Uri::create($this->request->module.'/view/'.$id));
		endif;

		//コメント入力viewを表示
		$view = \View::forge(LOCOMOPATH.'modules/workflow/views/comment.php');

		//add_actionset - back to edit
		$ctrl_url = \Inflector::ctrl_to_dir($this->request->controller);
		$action['urls'][] = \Html::anchor($ctrl_url.DS.'edit/'.$id,'戻る');
		$action['order'] = 10;
		\Actionset::add_actionset($this->request->controller, 'ctrl', $action);

		//assign
		$view->set_global('title', '承認申請');
		$view->set('button', '申請する');
		$view->set('id', $id);
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_approve()
	 */
	public function action_approve($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		//model and view
		$view = \View::forge(LOCOMOPATH.'modules/workflow/views/comment.php');
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		//postがあったら承認処理をして、閲覧画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = $model::get_route($this->request->controller, $id);
			if ($route_id):
				$comment = \Input::post('comment');

				//最後の承認かどうか確認する
				$current_step = $model::get_current_step($this->request->controller, $id) + 1;
				$total_step   = $model::get_total_step($route_id);
				$mode = $current_step == $total_step ? 'finish' : 'approve';

				//add_log
				$model::add_log($mode, $route_id, $this->request->controller, $id,$comment);

				//最後の承認であれば、項目のステータスを変更する
				if ($mode == 'finish'):
					$target_model = $this->model_name ;
					$obj = $target_model::find($id);
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
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_remand()
	 */
	public function action_remand($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		//postがあったら差し戻し処理をして、閲覧画面に戻る
		if (\Input::method() == 'POST'):
			$comment     = \Input::post('comment');
			$target_step = (int) \Input::post('target_step');
			$model::add_log('remand', null, $this->request->controller, $id, $comment, $target_step);
			\Session::set_flash('success', '差し戻し処理をしました');

			//差し戻しが最初まで戻った場合、in_progressを解除して編集できるようにする
			if ($target_step == -1):
				$target_model = $this->model_name ;
				$obj = $target_model::find($id);
				$obj->workflow_status = 'before_progress';
				$obj->save();
			endif;

			return \Response::redirect(\Uri::create(\Inflector::ctrl_to_dir($this->request->controller)));
		endif;

		//差し戻し候補を取得する
		$current_step = $model::get_current_step($this->request->controller, $id);
		$logs         = $model::get_route_logs($this->request->controller, $id, $current_step);
		$target_steps = array();

		foreach($logs as $log):
			//初期化と承認のログをとる
			if ($log->status == 'remand') continue;

			//現在のステップより上のログは除外する
			if ($log->current_step > $current_step) continue;

			//一つ下のステップに設定
			$target_step = (int) $log->current_step - 1;

			//複数回の差戻しによって同じステップ数を持つ場合は、keyで上書きする
			$user_info = \User\Model_User::find($log->did_user_id);
			$target_steps[$target_step] = $user_info->username;
		endforeach;

		//コメント入力viewを表示
		$view = \View::forge(LOCOMOPATH.'modules/workflow/views/comment.php');

		//assign
		$view->set_global('title', '差し戻し');
		$view->set('button',       '差し戻す');
		$view->set('target_steps', $target_steps);
		$view->set('id',           $id);
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_reject()
	 */
	public function action_reject($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		//model and view
		$view = \View::forge(LOCOMOPATH.'modules/workflow/views/comment.php');
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		//postがあったら経路設定して、編集画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = $model::get_route($this->request->controller, $id);

			if ($route_id):
				$comment = \Input::post('comment');
				$model::add_log('reject', $route_id, $this->request->controller, $id, $comment);
				\Session::set_flash('success', '項目を却下しました');

				//項目を削除する（可能であればソフトデリートする）
				$target_model = $this->model_name ;
				$obj = $target_model::find($id);
				$target_model::delete_item($obj);
				return \Response::redirect(\Uri::create($this->request->module));
			endif;
		endif;

		//assign
		$view->set_global('title', '却下の確認');
		$view->set('button', '却下する');
		$view->set('id', $id);
		$view->base_assign();
		$this->template->content = $view;
	}
}
