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
		if($this->is_workflowed):
			//error
			$model = $this->model_name ;
			if( ! \DBUtil::field_exists($model::get_table_name(), array('workflow_status'))):
				die('ワークフロー管理するコントローラにはworkflow_statusフィールドが必要です。');
			endif;
			//承認段階を確認し、最終承認がまだであれば常にworkflow_statusをin_progressにする
			if( ! \Kontiki\Model_Workflow_Abstract::check_workflow_staus($this->request->module, $obj->$primary_key[0])):
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

		//テンプレートをforge（revisionでも似た措置をしているので、ここ、あとで関数にまとめる）
		$tpl_path         = PKGPATH.'kontiki/modules/workflow/views/route.php';
		$tpl_path_default = PKGPATH.'kontiki/modules_default/workflow/views/route.php';
		if(file_exists($tpl_path)):
			$view = \View::forge($tpl_path);
		else:
			$view = \View::forge($tpl_path_default);
		endif;

		//モデル
		$model = \Kontiki\Model_Workflow_Abstract::forge();

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

		//コメント入力viewを表示


//あとで\Kontiki\Model_Workflow_Abstractを探してきちんと直す
		$model = \Kontiki\Model_Workflow_Abstract::forge();

		//現在のステップの確認
		$step = $model::get_current_step($this->request->module, $id);


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
			if( ! \Kontiki\Model_Workflow_Abstract::get_current_step($this->request->module, $obj->$primary_key[0])):
				$obj->workflow_status = 'in_progress';
				$obj->save();
			endif;
*/


		return \Response::redirect(\Uri::create($this->request->module.'/index_deleted'));
	}

}
