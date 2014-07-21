<?php
namespace Kontiki;
class Controller_Workflow_Abstract extends \Kontiki\Controller_Crud
{
	/**
	 * action_index_admin()
	 */
	public function action_index_admin($pagenum = 1)
	{

		$args = array(
			'pagenum'  => $pagenum,
			'template' => 'index_admin',
		);
		return self::index_core($args);
	}
}