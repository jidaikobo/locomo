<?php
namespace Kontiki;
abstract class Controller_Workflow extends \Kontiki\Controller
{
	/**
	 * post_save_hook()
	 */
	public function post_save_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);

		//workflow
		if(array_key_exists('workflow_actions', self::$actionset)):
			//error
			$model = $this->model_name ;
			if( ! \DBUtil::field_exists($model::get_table_name(), array('workflow_status'))):
				die('ワークフロー管理するコントローラにはworkflow_statusフィールドが必要です。');
			endif;
			//承認段階を確認し、最終承認がまだであれば常にworkflow_statusをin_progressにする
			if( ! \Workflow\Model_Workflow::check_workflow_staus($this->request->module, $obj->$primary_key[0])):
				$obj->workflow_status = 'in_progress';
				$obj->save();
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
			endif;
		endif;

		//設定されている経路をすべて取得
		$items = $model->find_items();

		//現在設定されている経路を取得
		$route_id = $model::get_route($this->request->module, $id);

		//assign
		$view->set_global('title', 'ルート設定');
		$view->set('items', $items);
		$view->set('route_id', $route_id);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}


	/**
	 * action_apply() at workflowed controller
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
		$view->set_global('title', 'コメント入力');
		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));

		//現在のステップの確認
		$step = $model::get_current_step($this->request->module, $id);

		$view = \View::forge(\Kontiki\Util::fetch_tpl('/workflow/views/route.php'));


echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;">' ;
var_dump( $step ) ;
echo '</textarea>' ;
die();

		//現在のステップが-1/Nだったら、ステップを一つ進める

echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;">' ;
var_dump( $step ) ;
echo '</textarea>' ;
die();

/*
承認段階を確認し、actionがapplyで、0/Nなら申請。
workflow_logs.current_stepを0にセット


actionがapproveか？
workflow_logs.current_stepを加算
承認段階が最後の一つであれば、項目のworkflow_statusをfinishedにセット

actionがremandか？
$progress（差し戻し先）を確認し、
workflow_logs.current_stepを当該差し戻し先に減算。

actionがrejectか？
項目のworkflow_statusをそのままにして、delete_atを付与（ソフトデリート）。

ステップに設定されているアクション（mail）を処理

mailアクション
ワークフロー関係者のメールアドレスを収集。

*/
/*
			//承認段階を確認し、最終承認がまだであれば常にworkflow_statusをin_progressにする
			if( ! \Workflow\Model_Workflow::get_current_step($this->request->module, $obj->$primary_key[0])):
				$obj->workflow_status = 'in_progress';
				$obj->save();
			endif;
*/


		return \Response::redirect(\Uri::create($this->request->module.'/index_deleted'));
	}

}
