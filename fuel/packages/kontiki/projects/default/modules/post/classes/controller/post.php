<?php
namespace Post;
class Controller_Post extends \Kontiki\Controller_Crud
{
	use \Workflow\Controller_Workflow;
	use \Revision\Controller_Revision;

	/**
	 * edit_core()
	 */
	public function edit_core($id = null, $obj = null, $redirect = null, $title = null)
	{
		return $this->workflow_edit_core($id, $obj, $redirect, $title);
	}

	/**
	 * revision_modify_data()
	 */
	public function revision_modify_data($obj, $mode = null)
	{
		if($mode == 'insert_revision'):
			$postcategories = is_array(\Input::post('postcategories')) ? \Input::post('postcategories') : array();
			$obj->postcategories = $postcategories;
		endif;
		return $obj;
	}

	/**
	 * pre_save_hook()
	 */
	public function pre_save_hook($obj = null, $mode = 'edit')
	{
		$obj = parent::post_save_hook($obj, $mode);
		$obj = $this->pre_workflow_save_hook($obj, $mode);
		return $obj;
	}

	/**
	 * post_save_hook()
	 */
	public function post_save_hook($obj = null, $mode = 'edit')
	{
		$obj = parent::post_save_hook($obj, $mode);
		$obj = $this->revision_save_hook($obj, $mode);
		$obj = $this->workflow_save_hook($obj, $mode);

		//postcategories
		$model = $this->model_name;
		$model::update_options_relations('postcategories', $obj->id);

		return $obj;
	}
}