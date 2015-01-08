<?php
namespace Locomo;
class Actionset_Base_Usr extends \Actionset_Base
{
	use \Actionset_Traits_Base_Revision;

	/**
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_admin($controller, $obj, $id);
		$actions = array(
			$controller.DS.'confirm_delete',
			$controller.DS.'create',
			$controller.DS.'delete',
			$controller.DS.'delete_deleted',
			$controller.DS.'each_index_revision',
			$controller.DS.'edit',
			$controller.DS.'edit_anyway',
			$controller.DS.'edit_deleted',
			$controller.DS.'index',
			$controller.DS.'index_admin',
			$controller.DS.'index_all',
			$controller.DS.'index_deleted',
			$controller.DS.'index_expired',
			$controller.DS.'index_invisible',
			$controller.DS.'index_revision',
			$controller.DS.'index_yet',
			$controller.DS.'undelete',
			$controller.DS.'view',
			$controller.DS.'view_anyway',
			$controller.DS.'view_deleted',
			$controller.DS.'view_expired',
			$controller.DS.'view_invisible',
			$controller.DS.'view_revision',
			$controller.DS.'view_yet',
		);
		\Arr::set($retvals, 'dependencies', $actions);
		return $retvals;
	}
}
