<?php
namespace Locomo;
class Actionset_Impt extends \Actionset_Base
{
//	use \Actionset_Traits_Revision;

	/**
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_admin($controller, $obj, $id);
		$actions = array(
			$controller.'/index_admin',
			$controller.'/index_deleted',
			$controller.'/preview',
			$controller.'/view',
			$controller.'/view_deleted',
			$controller.'/delete',
			$controller.'/undelete',
			$controller.'/confirm_delete',

			$controller.'/pdf_create',
			$controller.'/pdf_edit',
			$controller.'/pdf_edit_element',
			$controller.'/excel_create',
			$controller.'/excel_edit',
			$controller.'/excel_edit_element',
		);
		\Arr::set($retvals, 'dependencies', $actions);
		return $retvals;
	}


	/**
	 * index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::index_admin($controller, $obj, $id, $urls);
		return $retvals;
	}

	/**
	 * actionset_pdf_edit()
	 */
	public static function actionset_pdf_edit($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'realm'        => 'base',
			// 'urls'         => array(),
			'action_name'  => 'PDF編集',
			'show_at_top'  => true,
			'explanation'  => 'PDF用フォーマットを編集します。',
			'order'        => 10,
			'dependencies' => array(
				$controller.'/pdf_create',
			)
		);
		return $retvals;
	}
}
