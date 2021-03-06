<?php
namespace Locomo;
class Controller_Wrkflwadmin extends \Locomo\Controller_Base
{
	// locomo
	public static $locomo = array(
		'nicename'     => 'ワークフロー', // for human's name
		'explanation'  => 'ワークフロー経路の作成や編集を行います。',
		'main_action'  => 'index_admin', // main action
		'main_action_name' => 'ワークフロー管理', // main action's name
		'main_action_explanation' => 'ワークフロー経路の作成や編集を行います。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => true, // true: hide from admin bar
		'order'        => 1100, // order of appearance
		'no_acl'       => true, // true: admin's action. it will not appear at acl.
	);

	/**
	 * before()
	 */
	public function before()
	{
		parent::before();
		$this->model_name = '\\Model_Wrkflwadmin';
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		parent::index_admin();
	}

	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted()
	{
		parent::index_deleted();
	}

	/**
	 * action_view()
	 */
	public function action_view($id = null)
	{
		parent::view($id);
	}

	/**
	 * action_create()
	 */
	public function action_create()
	{
		parent::create();
	}

	/**
	 * action_edit()
	 */
	public function action_edit($id = null)
	{
		parent::edit($id);
	}

	/**
	 * action_delete()
	 */
	public function action_delete($id = null)
	{
		parent::delete($id);
	}

	/**
	 * action_undelete()
	 */
	public function action_undelete($id = null)
	{
		parent::undelete($id);
	}

	/**
	 * action_purge_confirm()
	 */
	public function action_purge_confirm($id = null)
	{
		parent::purge_confirm($id);
	}

	/**
	 * action_purge()
	 */
	public function action_purge($id = null)
	{
		parent::purge($id);
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
			if ( ! \Security::check_token())
			{
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			} else {
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
			if ( ! \Security::check_token())
			{
				\Session::set_flash('error', 'ワンタイムトークンが失効しています。送信し直してみてください。');
			} else {
				if ($model::update_workflow_setting($id, $steps))
				{
					\Session::set_flash('success', 'ワークフローを更新しました');
				}
				else
				{
					//いまのところtrueしか返らない。
					\Session::set_flash('error', 'ワークフローの更新に失敗しました');
				}
				\Response::redirect(\Uri::create('wrkflwadmin/setup/'.$id));
			}
		}

		//add_actionset - back to index at edit
		$action['urls'][] = \Html::anchor(static::$main_url, \Util::get_locomo(static::$controller, 'main_action_name', '一覧へ'));
		\Actionset::add_actionset(static::$controller, 'ctrl', $action);

		//assign
		$view = \View::forge('wrkflwadmin/setup');
		$view->set('stepnum', $stepnum);
		$view->set('allstep', $allstep);
		$view->set('steps', $steps);
		$view->set('workflow_id', $workflow->id);
		$view->set('workflow_name', $workflow->name);
		$view->set_global('title', 'ワークフローの設定');
		$this->template->content = $view;
	}
}