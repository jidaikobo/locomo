<?php
namespace Workflow;
class Controller_Workflowadmin extends \Locomo\Controller_Base
{
	use \Locomo\Controller_Traits_Crud;

	//locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 100,
		'is_for_admin' => true,
		'admin_home' => '\\Workflow\\Controller_Workflowadmin/index_admin',
		'admin_home_name' => '管理一覧',
		'nicename' => 'ワークフロー',
		'actionset_classes' =>array(
			'base'   => '\\Workflow\\Actionset_Base_Workflowadmin',
			'index'  => '\\Workflow\\Actionset_Index_Workflowadmin',
		),
	);

	/**
	 * action_index()
	 */
	public function action_index()
	{
		return \Response::redirect('/workflow/workflowadmin/index_admin');
	}

	/**
	 * action_setup()
	 */
	public function before()
	{
		parent::before();
		$this->model_name = '\\Workflow\\Model_Workflowadmin';
	}

	/**
	 * action_setup()
	 */
	public function action_setup($id = null)
	{
		is_null($id) and \Response::redirect($this->request->module);
		
		// get workflow name
		$model = $this->model_name ;
		$workflow = $model::find($id);
		if ( ! $workflow) \Response::redirect($this->request->module);

		// find_workflow_setting
		$steps = $model::find_workflow_setting($id);

		// for form value
		$allstep = \Input::post('allstep') ? intval(\Input::post('allstep')) : count($steps['allowers']);
		$allstep = ( ! $allstep) ? 1 : $allstep;
		$steps = \Input::post('steps') ?: $steps;
		if (\Input::post('steps'))
		{
			// unset empty values to tidt up
			foreach($steps['allowers'] as $key => $step)
			{
				// name is required.
				if (empty($step['name']))
				{
					unset($steps['allowers'][$key]);
				}
			}
			$allstep = count($steps['allowers']) + 1; // add step
		}

		// default step number
		$stepnum = array();
		for ($n = 1; $n <= $allstep; $n++)
		{
			$stepnum[] = $n;
		}

		// store to DB
		if ($steps && \Input::post('submit'))
		{
			if ($model::update_workflow_setting($id, $steps))
			{
				\Session::set_flash('success', 'ワークフローを更新しました');
			}
			else
			{
				//いまのところtrueしか返らない。
				\Session::set_flash('error', 'ワークフローの更新に失敗しました');
			}
			\Response::redirect(\Uri::create('workflow/workflowadmin/setup/'.$id));
		}

		//add_actionset - back to index at edit
		$ctrl_url = \Inflector::ctrl_to_dir($this->request->controller);
		$action['urls'][] = \Html::anchor($ctrl_url.DS.'index_admin/','一覧へ');
		$action['order'] = 10;
		\Actionset::add_actionset($this->request->controller, 'ctrl', $action);

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