<?php
namespace Locomo;
class Actionset_Frmt extends \Actionset_Base
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
	 * actionset_index_deleted()
	 */
	public static function actionset_index_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::index_deleted($controller, $obj, $id, $urls);
	}


	/**
	 * view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::view($controller, $obj, $id);
		\Arr::set($retvals, 'order', 3);
		return $retvals;
	}

	/**
	 * view_deleted()
	 */
	public static function view_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::view_deleted($controller, $obj, $id);
		return $retvals;
	}

	/**
	 * actionset_pdf_create()
	 */
	public static function actionset_pdf_create($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'realm'        => 'base',
			'urls'         => array(array($controller.DS."pdf_create", 'PDF新規作成')),
			'action_name'  => 'PDF新規作成',
			'show_at_top'  => true,
			'explanation'  => '新しいPDF用フォーマットを追加します。',
			'order'        => 10,
			'dependencies' => array(
				$controller.'/pdf_create',
			)
		);
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

	/**
	 * actionset_pdf_edit_element()
	 */
	public static function actionset_pdf_edit_element($controller, $obj = null, $id = null, $urls = array())
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

	/**
	 * actionset_pdf_create()
	 */
	public static function actionset_excel_create($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'realm'        => 'base',
			'urls'         => array(array($controller.DS."excel_create", 'EXCEL, CSV 新規作成')),
			'action_name'  => 'EXCEL新規作成',
			'show_at_top'  => true,
			'explanation'  => '新しいEXCEL CSV 用フォーマットを追加します。',
			'order'        => 20,
			'dependencies' => array(
				$controller.'/pdf_create',
			)
		);
		return $retvals;
	}
	/**
	 * actionset_excel_edit()
	 */
	public static function actionset_excel_edit($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			// 'realm'        => 'base',
			'urls'         => array(),
			'action_name'  => 'EXCEL, CSV 編集',
			'show_at_top'  => true,
			'explanation'  => 'EXCEL CSV 用フォーマットを編集します。',
			'order'        => 20,
			'dependencies' => array(
				$controller.'/pdf_create',
			)
		);
		return $retvals;
	}


}
