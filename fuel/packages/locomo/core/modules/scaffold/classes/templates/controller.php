<?php
namespace XXX;
class Controller_XXX extends \Locomo\Controller_Crud
{
	//trait
//	use \Option\Traits_Controller_Option;
//	use \Workflow\Traits_Controller_Workflow;
//	use \Revision\Traits_Controller_Revision;

	/**
	 * edit_core()
	 */
	public function edit_core($id = null, $obj = null, $redirect = null, $title = null)
	{
		return parent::edit_core($id, $obj, $redirect, $title);
//		return $this->workflow_edit_core($id, $obj, $redirect, $title);
	}

}

