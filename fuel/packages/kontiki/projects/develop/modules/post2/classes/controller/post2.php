<?php
namespace Post2;
class Controller_Post2 extends \Kontiki\Controller_Crud
{
	use \Revision\Controller_Revision;
	use \Workflow\Controller_Workflow;

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
//		return parent::edit_core($id, $obj, $redirect, $title);
		return $this->workflow_edit_core($id, $obj, $redirect, $title);
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
		$obj = parent::pre_save_hook($obj, $mode);
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
		return $obj;
	}
}

