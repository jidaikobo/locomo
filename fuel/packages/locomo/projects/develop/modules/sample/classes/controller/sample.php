<?php
namespace Sample;
\Module::load('bulk');
class Controller_Sample extends \Locomo\Controller_Crud
{
	//trait
	use \Bulk\Traits_Controller_Bulk;
//	use \Workflow\Controller_Workflow;
//	use \Revision\Controller_Revision;

	/**
	 * set_actionset()
	 */
	public static function set_actionset($obj = null)
	{
		parent::set_actionset($obj);
	}

	/**
	 * edit_core()
	 */
	public function edit_core($id = null, $obj = null, $redirect = null, $title = null)
	{
		return parent::edit_core($id, $obj, $redirect, $title);
//		return $this->workflow_edit_core($id, $obj, $redirect, $title);
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


	public function action_sample()
	{
		$this->template = 'default';

		$view = \View::forge(PKGCOREPATH . 'modules/bulk/views/bulk.php');
		$form = $this->bulk(array(), null, 'disabled', true, null, 'bulk_ctm');

		$view->set_global('title', 'サンプルバルク');
		$view->set_global('form', $form, false);
		$view->base_assign();
		$this->template->content = $view;
	}

}

