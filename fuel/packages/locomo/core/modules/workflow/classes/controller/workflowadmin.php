<?php
namespace Workflow;
class Controller_Workflowadmin extends \Locomo\Controller_Crud
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 100,
		'is_for_admin' => true,
		'nicename' => 'ワークフロー',
		'actionset_classes' =>array(
			'base'   => '\\Workflowadmin\\Actionset_Base_Workflowadmin',
			'index'  => '\\Workflowadmin\\Actionset_Index_Workflowadmin',
		),
	);

	/**
	 * action_setup()
	 */
	public function before()
	{
		parent::before();
		$this->model_name = '\\Workflow\\Model_Workflowadmin';
	}

	/**
	 * action_edit()
	 */
	public function action_edit($id = null) {
		parent::action_edit($id);
	}

	/**
	 * action_setup()
	 */
	public function action_setup($id = null)
	{
		is_null($id) and \Response::redirect($this->request->module);
		
		//ワークフロー名取得
		$model = $this->model_name ;
		$workflow = $model::find($id);
		if( ! $workflow) \Response::redirect($this->request->module);

		//dbから取得
		$steps = $model::find_workflow_setting($id);

		//フォーム用に値の保存（いろいろと手抜きだけど、管理者専用なのでとりあえず実装を優先する）
		$allstep = \Input::post('allstep') ? intval(\Input::post('allstep')) : count($steps);
		$allstep = ( ! $allstep) ? 1 : $allstep;
		$steps = \Input::post('steps') ?: $steps;
		if(\Input::post('steps')):
			//post値が空だったらunsetして整理
			foreach($steps as $key => $step):
				if(
					empty($step['name']) &&
					$step['condition'] == 'none' &&
					empty($step['allowers']) &&
					empty($step['actions'])
				):
					unset($steps[$key]);
				endif;
			endforeach;
			$allstep = count($steps) + 1;
		endif;

		//ステップ数デフォルト
		$stepnum = array();
		for($n = 1; $n <= $allstep; $n++):
			$stepnum[] = $n;
		endfor;

		//DBに保存
		if($steps && \Input::post('submit')):
			if($model::update_workflow($id, $steps)):
				\Session::set_flash('success', 'ワークフローを更新しました');
			else:
				//いまのところtrueしか返らない。
				\Session::set_flash('error', 'ワークフローの更新に失敗しました');
			endif;
			\Response::redirect(\Uri::create('workflowadmin/setup/'.$id));
		endif;

		//assign
		$view = \View::forge('setup');
		$view->set('stepnum', $stepnum);
		$view->set('allstep', $allstep);
		$view->set('steps', $steps);
		$view->set('workflow_id', $workflow->id);
		$view->set('workflow_name', $workflow->name);
		$view->set_global('title', 'ワークフローの設定');
		$view->base_assign();
		$this->template->content = $view;
	}
}