<?php
namespace Test;
class Controller_Test extends \Kontiki\Controller_Crud
{
//	use \Workflow\Controller_Workflow;
//	use \Revision\Controller_Revision;

	/**
	 * set_actionset()
	 */
	public function set_actionset($controller = null, $id = null)
	{
		parent::set_actionset($controller, $id);
	}

	/**
	 * edit_core()
	 */
	public function edit_core($id = null, $obj = null, $redirect = null, $title = null)
	{
		if($id == null || $obj == null || $redirect == null) \Response::redirect($this->request->module);

		$model = $this->model_name ;
		$form = $model::form_definition('edit');
		$view = \View::forge('edit');

		//validation succeed
		if ($form->validation()->run() && \Security::check_token()):

			//prepare self fields
			foreach(\Input::post() as $field => $value):
				if( ! \DBUtil::field_exists($model::get_table_name(), array($field))) continue;
				$obj->$field = $value;
			endforeach;

			//pre_save_hook
			$obj = $this->pre_save_hook($obj, 'edit');

			//save
			if ($obj->save()):

				//post_save_hook
				$obj = $this->post_save_hook($obj, 'edit');

				//save relations
//				$model::delete_relations($obj->id);
//				$obj = $model::insert_relations($obj->id);

				//message
				\Session::set_flash(
					'success',
					sprintf($this->messages['edit_success'], self::$nicename, $id)
				);
				\Response::redirect($redirect);
			else:
				\Session::set_flash(
					'error',
					sprintf($this->messages['edit_error'], self::$nicename, $id)
				);
			endif;
		//edit view or validation failed of CSRF suspected
		else:
			if (\Input::method() == 'POST'):
				foreach(\Input::post() as $k => $v):
					if($k == 'submit') continue;
					$obj->$k = $v;
				endforeach;
				\Session::set_flash('error', $form->error());
			endif;

			$view->set_global('item', $obj, false);
		endif;

		//view
		$view->set_global('title', sprintf($this->titles['edit'], self::$nicename));
		$view->set_global('form', $form, false);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_create()
	 */
	public function action_create()
	{
		$model = $this->model_name ;
		$form = $model::form_definition('create');

		if (\Input::method() == 'POST'):

			if ($form->validation()->run() && \Security::check_token()):
				$args = array();
				foreach(\Input::post() as $field => $value):
					if( ! \DBUtil::field_exists($model::get_table_name(), array($field))) continue;
					$args[$field] = $value;
				endforeach;

				$obj = $model::forge($args);

				//pre_save_hook
				$obj = $this->pre_save_hook($obj, 'create');

				if ($obj and $obj->save()):

					//post_save_hook
					$obj = $this->post_save_hook($obj, 'create');

					//save relations
//					$obj = $model::insert_relations($obj->id);
					
					\Session::set_flash(
						'success',
						sprintf($this->messages['create_success'], self::$nicename, $obj->id)
					);
					\Response::redirect(\Uri::create($this->request->module.'/edit/'.$obj->id));
				else:
					\Session::set_flash(
						'error',
						sprintf($this->messages['create_error'], self::$nicename)
					);
				endif;
			else:
				$form->repopulate();
				\Session::set_flash('error', $form->error());
			endif;
		endif;

		//view
		$view = \View::forge('create');
		$view->set_global('title', sprintf($this->titles['create'], self::$nicename));
		$view->set_global('form', $form, false);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}


	/**
	 * revision_modify_data()
	 */
	public function revision_modify_data($obj, $mode = null)
	{
		return $obj;
	}

	/**
	 * pre_save_hook()
	 */
	public function pre_save_hook($obj = null, $mode = 'edit')
	{
		$obj = parent::post_save_hook($obj, $mode);
//		$obj = $this->pre_workflow_save_hook($obj, $mode);
		return $obj;
	}

	/**
	 * post_save_hook()
	 */
	public function post_save_hook($obj = null, $mode = 'edit')
	{
		$obj = parent::post_save_hook($obj, $mode);
//		$obj = $this->revision_save_hook($obj, $mode);
//		$obj = $this->workflow_save_hook($obj, $mode);
		return $obj;
	}
}

