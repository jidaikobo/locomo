<?php
namespace Locomo;
trait Controller_Traits_Wrkflw
{
	/**
	 * action_index_workflow()
	 */
	public function action_index_workflow($controller = null)
	{
		// model and view
		$view = \View::forge('wrkflw/index_workflow');
		$controller = $controller ?: get_called_class();
		$model_name = str_replace('Controller', 'Model', $controller);
		$model = $model_name::forge();

		// get related unfinished items
		$current_items = $model->get_related_current_items($controller, $model);

		// order by is current action?
		$related = array();
		$not_related = array();
		foreach ($current_items as $id => $current_item)
		{
			if (in_array(\Auth::get('id'), $current_item->workflow_users))
			{
				$related[] = $current_item;
			}
			else
			{
				$not_related[] = $current_item;
			}
		}
		$related+= $not_related;

		// 進行中の件数
		$count = $model::count(array('where'=>array(array('workflow_status', '<>', 'finish')),));

		// assign
		$view->set_global('title', '関連ワークフロー項目');
		$view->set('controller_uri', \Inflector::ctrl_to_dir($controller));
		$view->set('pk', $model->get_primary_keys('first'));
		$view->set('count', $count);
		$view->set('subject_field', $model::get_default_field_name('subject'));
		$view->set('related', $related);
//		$view->set('not_related', $not_related);
		$this->template->content = $view;
	}

	/**
	 * action_route()
	 */
	public function action_route($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		// model and view
		$view = \View::forge('wrkflw/route');
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		// postがあったら経路設定して、表示画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = \Input::post('route');
			if ($route_id):
				$model::set_route($route_id, \Request::active()->controller, $id);
				\Session::set_flash('success', 'ルートを設定しました');

				// update workflow_status
				$obj = $model::find($id);
				$obj->workflow_status = 'init';
				$obj->save();

				// ルート設定したら編集画面に返す
				return \Response::redirect(\Uri::create(\Inflector::ctrl_to_dir(\Request::active()->controller.'/edit/'.$id)));
			else:
				\Session::set_flash('error', 'ルートを選択してください');
			endif;
		endif;

		// 設定されている経路をすべて取得
		$model_wfadmin = \Model_Wrkflwadmin::forge();
		$items = $model_wfadmin->find('all');

		// 現在設定されている経路を取得（将来のルート変更用）
		$route_id = $model::get_route(\Request::active()->controller, $id);

		// add_actionset - back to edit
		$ctrl_url = \Inflector::ctrl_to_dir(\Request::active()->controller);
		$action['urls'][] = \Html::anchor($ctrl_url.DS.'edit/'.$id,'戻る');
		$action['order'] = 10;
		\Actionset::add_actionset(\Request::active()->controller, 'ctrl', $action);

		// assign
		$view->set_global('title', 'ルート設定');
		$view->set('button', '申請する');
		$view->set('items', $items);
		$view->set('route_id', $route_id);
		$view->set('item_id', $id);
		$this->template->content = $view;
	}

	/**
	 * action_apply()
	 */
	public function action_apply($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		$controller = get_called_class();
		$model_name = str_replace('Controller', 'Model', $controller);
		$model = $model_name::forge();
		$redirect = \Inflector::ctrl_to_dir($controller);

		// route_idがないということは経路設定前なので、経路設定にリダイレクト
		$route_id = $model::get_route(\Request::active()->controller, $id);
		if ( ! $route_id)
		{
			\Session::set_flash('error', '承認申請するためには、経路設定をしてください。');
			return \Response::redirect(\Uri::create($redirect.'/route/'.$id));
		}

		// postがあったら申請処理をして、編集画面に戻る
		if (\Input::method() == 'POST'):
			if (\Security::check_token()):
				$comment = \Input::post('comment');
				$model::add_log('approve', null, $controller, $id, $comment);
				\Session::set_flash('success', '申請しました');
	
				// 項目のworkflow_statusをin_progressにする（編集できないようにする）
				$target_model = $this->model_name ;
				$obj = $target_model::find($id);
				$obj->workflow_status = 'in_progress';
				$obj->save();
				return \Response::redirect(\Uri::create($redirect.'/view/'.$id));
			else:
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			endif;
		endif;

		// コメント入力viewを表示
		$view = \View::forge('wrkflw/comment');

		// add_actionset - back to edit
		$ctrl_url = \Inflector::ctrl_to_dir($controller);
		$action['urls'][] = \Html::anchor($ctrl_url.DS.'edit/'.$id,'戻る');
		$action['order'] = 10;
		\Actionset::add_actionset($controller, 'ctrl', $action);

		// assign
		$view->set_global('title', '承認申請');
		$view->set('button', '申請する');
		$view->set('id', $id);
		$this->template->content = $view;
	}

	/**
	 * action_approve()
	 */
	public function action_approve($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		// model and view
		$view = \View::forge('wrkflw/comment');
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		// redirect
		$redirect = \Inflector::ctrl_to_dir(\Request::active()->controller);

		// postがあったら承認処理をして、閲覧画面に戻る
		if (\Input::method() == 'POST'):
			if (\Security::check_token()):
				$route_id = $model::get_route(\Request::active()->controller, $id);
				if ($route_id):
					$comment = \Input::post('comment');
	
					// 最後の承認かどうか確認する
					$current_step = $model::get_current_step(\Request::active()->controller, $id) + 1;
					$total_step   = $model::get_total_step($route_id);
					$mode = $current_step == $total_step ? 'finish' : 'approve';
	
					// add_log
					$model::add_log($mode, $route_id, \Request::active()->controller, $id,$comment);
	
					// 最後の承認であれば、項目のステータスを変更する
					if ($mode == 'finish'):
						$target_model = $this->model_name ;
						$obj = $target_model::find($id);
						$obj->workflow_status = 'finish';
						$obj->save();
						\Session::set_flash('success', '最終の承認をしました');
					else:
						\Session::set_flash('success', '承認しました');
					endif;
	
					return \Response::redirect(\Uri::create($redirect.'/view/'.$id));
				else:
					\Session::set_flash('error', '承認ルートを見つけられませんでした。');
				endif;
			else:
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			endif;
		endif;

		// assign
		$view->set_global('title', '承認');
		$view->set('button', '承認する');
		$view->set('id', $id);
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

		// postがあったら差し戻し処理をして、閲覧画面に戻る
		if (\Input::method() == 'POST'):
			$comment     = \Input::post('comment');
			$target_step = (int) \Input::post('target_step');
			$model::add_log('remand', null, \Request::active()->controller, $id, $comment, $target_step);
			\Session::set_flash('success', '差し戻し処理をしました');

			// 差し戻しが最初まで戻った場合、in_progressを解除して編集できるようにする
			if ($target_step == -1):
				$target_model = $this->model_name ;
				$obj = $target_model::find($id);
				$obj->workflow_status = 'before_progress';
				$obj->save();
			endif;
			
			$redirect = \Uri::create(\Inflector::ctrl_to_dir(\Request::active()->controller));
			return \Response::redirect($redirect);
		endif;

		// 差し戻し候補を取得する
		$current_step = $model::get_current_step(\Request::active()->controller, $id);
		$logs         = $model::get_route_logs(\Request::active()->controller, $id, $current_step);
		$target_steps = array();

		// 名前表示用にステップを取得
		$route_id = $model::get_route(\Request::active()->controller, $id);
		$q = \DB::select('*');
		$q->from('lcm_wrkflw_steps');
		$q->where('workflow_id', $route_id);
		$q->order_by('seq', 'ASC');
		$steps = $q->execute()->as_array('seq');

		foreach($logs as $log):
			// 初期化と承認のログをとる
			if ($log->status == 'remand') continue;

			// 現在のステップより上のログは除外する
			if ($log->current_step > $current_step) continue;

			// 一つ下のステップに設定
			$target_step = (int) $log->current_step - 1;

			// ステップ名
			$step_name = \Arr::get($steps, "{$log->current_step}.name", '作成');

			// 複数回の差戻しによって同じステップ数を持つ場合は、keyで上書きする
			$user_info = \Model_Usr::find($log->did_user_id);
			$target_steps[$target_step] = $user_info->username." ({$step_name}の段階)";
		endforeach;

		// コメント入力viewを表示
		$view = \View::forge('wrkflw/comment');

		// assign
		$view->set_global('title', '差し戻し');
		$view->set('button',       '差し戻す');
		$view->set('target_steps', $target_steps);
		$view->set('id',           $id);
		$this->template->content = $view;
	}

	/**
	 * action_reject()
	 */
	public function action_reject($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		// model and view
		$view = \View::forge('wrkflw/comment');
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		// postがあったら経路設定して、編集画面に戻る
		if (\Input::method() == 'POST'):
			$route_id = $model::get_route(\Request::active()->controller, $id);

			if ($route_id):
				$comment = \Input::post('comment');
				$model::add_log('reject', $route_id, \Request::active()->controller, $id, $comment);

				$controller = \Request::active()->controller;

echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">' ;
var_dump( $controller ) ;
echo '</textarea>' ;
die();


				$redirect = \Inflector::ctrl_to_dir(\Request::active()->controller);

				// 項目を削除する（可能であればソフトデリートする）
				$target_model = $this->model_name ;
				$obj = $target_model::find($id);
				if ( ! $obj)
				{
					\Session::set_flash('success', '存在しない項目を却下しようとしました。');
					return \Response::redirect(\Uri::create($redirect));
				}

				if (isset($obj::properties()['deleted_at']))
				{
					$obj->delete();
				} else {
					$obj->purge(null, true);
				}
				\Session::set_flash('success', '項目を却下しました');

				return \Response::redirect(\Uri::create($redirect));
			endif;
		endif;

		// assign
		$view->set_global('title', '却下の確認');
		$view->set('button', '却下する');
		$view->set('id', $id);
		$this->template->content = $view;
	}
}
