<?php
namespace Locomo;
trait Controller_Traits_Wrkflw
{
	/**
	 * _init()
	 */
	public static function _init()
	{
		// event - locomo_edit_not_found
		\Event::register('locomo_edit_not_found', function(){
			\Session::set_flash('error', '削除されているか、承認プロセス進行中の項目は編集できません。');
			\Response::redirect(static::$main_url);
		});

		// event - locomo_delete
		\Event::register('locomo_delete', function($obj){
			if ( ! isset($obj->workflow_status)) return;
			if ($obj->workflow_status == 'in_progress')
			{
				\Session::set_flash('error', '承認プロセス進行中の項目は削除できません。');
				return \Response::redirect(static::$main_url);
			}
			return;
		});

		// event - locomo_revision_update
		\Event::register('locomo_revision_update', function($operation_and_comment){
			if (\Request::main()->action == 'route')
			{
				$id = \Input::post('route', 0);
				$obj = \Model_Wrkflwadmin::find($id);
				return array('routing', is_object($obj) ? '経路名：'.$obj->name : '');
			}
			$cmt = \Input::post('comment', '');
			if (\Request::main()->action == 'apply')   return array('apply',   $cmt);
			if (\Request::main()->action == 'remand')  return array('remand',  $cmt);
			if (\Request::main()->action == 'approve') return array('approve', $cmt);
			if (\Request::main()->action == 'reject')  return array('reject',  $cmt);
			if (\Request::main()->action == 'withdraw')  return array('withdraw',  $cmt);
			return $operation_and_comment;
		});
	}

	/**
	 * action_index_workflow()
	 */
	public function action_index_workflow($size = 1, $controller = null)
	{
		// model and view
		$view = \View::forge('wrkflw/index_workflow');
		$controller = $controller ?: get_called_class();
		$model_name = str_replace('Controller', 'Model', $controller);
		$model = clone $model_name::forge();

		// get related unfinished items
		$current_items = $model->get_related_current_items($controller, $model);

		// column
		$column = \Arr::get($model::get_field_by_role('creator_id'), 'lcm_field', 'creator_id');

		// order by is current action?
		$related = array();
		$not_related = array();
		foreach ($current_items as $id => $current_item)
		{
			$related_users = $current_item->workflow_users + array($current_item->{$column});
			if (in_array(\Auth::get('id'), $related_users))
			{
				$related[] = $current_item;
			}
			else
			{
				$not_related[] = $current_item;
			}
		}

		foreach($not_related as $v) {
			array_push($related, $v);
		}

		// 進行中の件数
		$count = $model::count(array('where'=>array(array('workflow_status', '<>', 'finish')),));

		// 表示権限を厳密にとるためモデルのキャッシュを削除しset_authorized_options()を設定する
//		$model::clear_cached_objects();
//		$model::set_authorized_options(); $model::$_options;

		// assign
		$view->set_global('title', '関連ワークフロー項目');
		$view->set('controller_uri', \Inflector::ctrl_to_dir($controller));
		$view->set('pk', $model::primary_key()[0]);
		$view->set('model', $model);
		$view->set('count', $count);
		$view->set('subject_field', \Arr::get($model::get_field_by_role('subject'), 'lcm_field'));
		$view->set('related', $related);
//		$view->set('not_related', $not_related);
		$this->template->content = $view;
	}

	/**
	 * action_route()
	 */
	public function action_route($id)
	{
		// model and view
		$view = \View::forge('wrkflw/route');
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		// $obj
		$obj = $model::find($id);
		if ( ! $obj)
		{
			\Session::set_flash('error', 'ルート設定すべき項目が見つかりませんでした。');
			return \Response::redirect(static::$main_url);
		}

		// postがあったら経路設定して、表示画面に戻る
		if (\Input::method() == 'POST')
		{
			if ( ! \Security::check_token())
			{
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			}
			else
			{
				$route_id = \Input::post('route');
				if ($route_id)
				{
					$model::set_route($route_id, \Request::active()->controller, $id);
					\Session::set_flash('success', 'ルートを設定しました');

					// update workflow_status
					$obj->workflow_status = 'init';
					$obj->save();

					// ルート設定したら編集画面に返す
					return \Response::redirect(static::$base_url.'edit/'.$id);
				}
				else
				{
					\Session::set_flash('error', 'ルートを選択してください');
				}
			}
		}

		// 設定されている経路をすべて取得
		$model_wfadmin = \Model_Wrkflwadmin::forge();
		$items = $model_wfadmin->find('all');

		// 自分に関係のないワークフローをハツる
		if ( ! in_array(\Auth::get('id'), array(-1,-2)))
		{
			foreach ($items as $k => $item)
			{
				$writers = \Model_Wrkflwadmin::find_writers($item->id);
				if ( ! in_array(\Auth::get('id'), $writers['allusers']))
				{
					unset($items[$k]);
				}
			}
		}

		// 現在設定されている経路を取得（ルート変更用）
		$route_id = $model::get_route(\Request::active()->controller, $id);

		// add_actionset - back to edit
		$action['urls'][] = \Html::anchor(static::$base_url.'edit/'.$id,'戻る');
		\Actionset::add_actionset(\Request::active()->controller, 'ctrl', $action);

		// subject
		$subject = \Arr::get($model::get_field_by_role('subject'), 'lcm_field', '');
		if ( ! $subject) throw new \Exception('対象modelで、lcm_roleがsubjectになったものを設定してください。');

		// assign
		$view->set_global('title', 'ルート設定');
		$view->set('subject', $subject);
		$view->set('obj', $obj);
		$view->set('button', '申請');
		$view->set('items', $items);
		$view->set('route_id', $route_id);
		$view->set('item_id', $id);
		$this->template->content = $view;
	}

	/**
	 * action_apply()
	 * 執筆者による承認申請
	 */
	public function action_apply($id)
	{
		$controller = \Request::active()->controller;
		$model_name = str_replace('Controller', 'Model', $controller);
		$model = $model_name::forge();

		// route_idがないということは経路設定前なので、経路設定にリダイレクト
		$route_id = $model::get_route($controller, $id);
		if ( ! $route_id)
		{
			\Session::set_flash('error', '承認申請するためには、経路設定をしてください。');
			return \Response::redirect(static::$base_url.'route/'.$id);
		}

		// 権限確認
		$target_model = $this->model_name ;
		$obj = $target_model::find($id);
		if ( ! $obj)
		{
			\Session::set_flash('error', '存在しない項目です。');
			return \Response::redirect(static::$main_url);
		}
		if ($obj->workflow_status !== 'init')
		{
			\Session::set_flash('error', '承認申請はルート設定直後のみ行うことができます。');
			return \Response::redirect(static::$base_url.'edit/'.$id);
		}

		// postがあったら申請処理をして、編集画面に戻る
		if (\Input::method() == 'POST')
		{
			if ( ! \Security::check_token())
			{
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			}
			else
			{
				$comment = \Input::post('comment');
				$model::add_log('approve', null, $controller, $id, $comment);
				\Session::set_flash('success', '申請しました');

				// 項目のworkflow_statusをin_progressにする（編集できないようにする）
				$obj->workflow_status = 'in_progress';
				$obj->save();
				return \Response::redirect(static::$base_url.'view/'.$id);
			}
		}

		// コメント入力viewを表示
		$view = \View::forge('wrkflw/comment');

		// add_actionset - back to edit
		$ctrl_url = \Inflector::ctrl_to_dir($controller);
		$action['urls'][] = \Html::anchor($ctrl_url.DS.'edit/'.$id,'戻る');
		\Actionset::add_actionset($controller, 'ctrl', $action);

		// assign
		static::set_route_info($view, $controller, $id);
		$view->set_global('title', '承認申請');
		$view->set('button', '申請');
		$this->template->content = $view;
	}

	/**
	 * action_approve()
	 * 承認
	 */
	public function action_approve($id)
	{
		// model and view
		$model = $this->model_name ;
		$view = \View::forge('wrkflw/comment');
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();
		$controller = \Request::active()->controller;

		// postがあったら承認処理をして、閲覧画面に戻る
		if (\Input::method() == 'POST')
		{
			if ( ! \Security::check_token())
			{
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			}
			else
			{
				$route_id = $model::get_route($controller, $id);
				if ($route_id)
				{
					$comment = \Input::post('comment');

					// 妥当な段階の承認か確認する
					$current_step = $model::get_current_step($controller, $id) ;
					$step_id = $model::get_current_step_id($route_id, $current_step);
					if ( ! in_array(\Auth::get('id'), $model::get_members($step_id)))
					{
						\Session::set_flash('error', '承認済みです。');
						return \Response::redirect(static::$main_url);
					}

					// 最後の承認かどうか確認する
					$total_step   = $model::get_total_step($route_id);
					$mode = $current_step + 1 == $total_step ? 'finish' : 'approve';

					// add_log
					$model::add_log($mode, $route_id, $controller, $id,$comment);

					// 最後の承認であれば、項目のステータスを変更する
					if ($mode == 'finish')
					{
						$target_model = $this->model_name ;
						$obj = $target_model::find($id);
						$obj->workflow_status = 'finish';
						$obj->save();
						\Session::set_flash('success', '最終の承認をしました');
					}
					else
					{
						\Session::set_flash('success', '承認しました');
					}

//					return \Response::redirect(static::$base_url.'view/'.$id);
					return \Response::redirect(static::$base_url.'index_workflow/');
				}
				else
				{
					\Session::set_flash('error', '承認ルートを見つけられませんでした。');
				}
			}
		}

		// assign
		static::set_route_info($view, $controller, $id);
		$view->set_global('title', '承認');
		$view->set('button', '承認');
		$this->template->content = $view;
	}

	/**
	 * action_remand()
	 * 差し戻し
	 */
	public function action_remand($id)
	{
		$controller = \Request::active()->controller;
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		// postがあったら差し戻し処理をして、閲覧画面に戻る
		if (\Input::method() == 'POST')
		{
			if ( ! \Security::check_token())
			{
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			}
			else
			{
				$comment     = \Input::post('comment');
				$target_step = (int) \Input::post('target_step');
				$model::add_log('remand', null, $controller, $id, $comment, $target_step);
				\Session::set_flash('success', '差し戻し処理をしました');

				// 差し戻しが最初まで戻った場合、in_progressを解除して編集できるようにする
				if ($target_step == -1)
				{
					$target_model = $this->model_name ;
					$obj = $target_model::find($id);
					$obj->workflow_status = 'before_progress';
					$obj->save();
				}
				return \Response::redirect(static::$main_url);
			}
		}

		// 差し戻し候補を取得する
		$current_step = $model::get_current_step($controller, $id);
		$logs         = $model::get_route_logs($controller, $id, $current_step);
		$target_steps = array();

		// 名前表示用にステップを取得
		$route_id = $model::get_route($controller, $id);
		$q = \DB::select('*');
		$q->from('lcm_wrkflw_steps');
		$q->where('workflow_id', $route_id);
		$q->order_by('seq', 'ASC');
		$steps = $q->execute()->as_array('seq');

		foreach($logs as $log)
		{
			// 初期化と承認のログをとる
			if ($log->status == 'remand') continue;

			// 現在のステップより上のログは除外する
			if ($log->current_step > $current_step) continue;

			// 一つ下のステップに設定
			$target_step = (int) $log->current_step - 1;

			// ステップ名
			$step_name = \Arr::get($steps, "{$log->current_step}.name", '作成');

			// 複数回の差戻しによって同じステップ数を持つ場合は、keyで上書きする
			$target_steps[$target_step] = \Model_Usr::get_display_name($log->did_user_id)." ({$step_name}の段階)";
		}

		// コメント入力viewを表示
		$view = \View::forge('wrkflw/comment');

		// assign
		static::set_route_info($view, $controller, $id);
		$view->set_global('title', '差し戻し');
		$view->set('button', '差し戻し');
		$view->set('target_steps', $target_steps);
		$this->template->content = $view;
	}

	/**
	 * action_withdraw()
	 * 取り下げ
	 */
	public function action_withdraw($id)
	{
		$controller = \Request::active()->controller;
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		// postがあったら取り下げ処理をして、閲覧画面に戻る
		if (\Input::method() == 'POST')
		{
			if ( ! \Security::check_token())
			{
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			}
			else
			{
				$comment     = \Input::post('comment');
				$target_step = (int) \Input::post('target_step');
				$model::add_log('withdraw', null, $controller, $id, $comment, $target_step);
				\Session::set_flash('success', '取り下げ処理をしました');

				// 取り下げたので、in_progressを解除して編集できるようにする
				$target_model = $this->model_name ;
				$obj = $target_model::find($id);
				$obj->workflow_status = 'before_progress';
				$obj->save();

				if (method_exists(\Request::active()->controller, 'action_view'))
				{
					return \Response::redirect(static::$base_url.'/view/'.$id);
				}
				else
				{
					return \Response::redirect(static::$main_url);
				}
			}
		}

		// コメント入力viewを表示
		$view = \View::forge('wrkflw/comment');

		// assign
		static::set_route_info($view, $controller, $id);
		$view->set_global('title', '取り下げ');
		$view->set('button', '取り下げ');
		$this->template->content = $view;
	}

	/**
	 * action_reject()
	 * 却下（delete）
	 */
	public function action_reject($id)
	{
		// model and view
		$controller = \Request::active()->controller;
		$view = \View::forge('wrkflw/comment');
		$model_name = str_replace('Controller', 'Model', get_called_class());
		$model = $model_name::forge();

		// postがあったら経路設定して、編集画面に戻る
		if (\Input::method() == 'POST')
		{
			if ( ! \Security::check_token())
			{
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			}
			else
			{
				$route_id = $model::get_route($controller, $id);

				if ($route_id)
				{
					$comment = \Input::post('comment');
					$model::add_log('reject', $route_id, $controller, $id, $comment);

					// 項目を削除する（可能であればソフトデリートする）
					$target_model = $this->model_name ;
					$obj = $target_model::find($id);
					if ( ! $obj)
					{
						\Session::set_flash('success', '存在しない項目を却下しようとしました。');
						return \Response::redirect(static::$main_url);
					}

					if (isset($obj::properties()['deleted_at']))
					{
						$obj->delete();
					}
					else
					{
						$obj->purge(null, true);
					}
					\Session::set_flash('success', '項目を却下しました。');

					return \Response::redirect(static::$main_url);
				}
			}
		}

		// assign
		static::set_route_info($view, $controller, $id);
		$view->set_global('title', '却下の確認');
		$view->set('button', '却下');
		$this->template->content = $view;
	}

	/**
	 * get_userinfo()
	 * ユーザ情報を取得する
	 * @return (object)
	 */
/*
	public static function userinfo($uid)
	{
		$user_info = \Model_Usr::find($uid);

		// \Model_Usrで見つからなかったら管理者ユーザ
		if ( ! $user_info)
		{
			$admins = [-1 => '管理者', -2 => 'root管理者'];
			$user_info = (object) array();
			$user_info->display_name = $admins[$uid];
		}
		if ( ! $user_info)
		{
		}

		return $user_info;
	}
*/

	/**
	 * set_route_info()
	 * どのアクションでも使うのでメンバ変数に持たせる
	 */
	public function set_route_info($view, $controller, $obj_id)
	{
		$model = \Inflector::maybe_ctrl_to_model($controller);
		$route_id = $model::get_route($controller, $obj_id); // AKA workflow_id
//		$logs     = $model::get_strait_route_logs($controller, $obj_id);

		// assign
		$view->set('id', $obj_id);
		$view->set('current_step', $model::get_current_step($controller, $obj_id) + 1);
		$view->set('total_step', $model::get_total_step($route_id));
		$view->set('workflow', \Model_Wrkflwadmin::find($route_id));
	}
}
