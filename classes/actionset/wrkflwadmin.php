<?php
namespace Locomo;
class Actionset_Wrkflwadmin extends \Actionset_Base
{
	/**
	 * view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::view($controller, $obj, $id, $urls);
	}

	/**
	 * edit()
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::edit($controller, $obj, $id, $urls);
	}
	
	/**
	 * create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::create($controller, $obj, $id, $urls);
	}

	/**
	 * delete()
	 */
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::delete($controller, $obj, $id, $urls);
	}

	/**
	 * undelete()
	 */
	public static function actionset_undelete($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::undelete($controller, $obj, $id, $urls);
	}

	/**
	 * delete_deleted()
	 */
	public static function actionset_purge_confirm($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::purge_confirm($controller, $obj, $id, $urls);
	}

	/**
	 * index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::index_admin($controller, $obj, $id, $urls);
	}

	/**
	 * index_deleted()
	 */
	public static function actionset_index_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::index_deleted($controller, $obj, $id, $urls);
	}

	/**
	 * setup()
	 */
	public static function actionset_setup($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'view' && $id):
			$urls = array(array($controller.DS."setup/".$id, '設定'));
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフローの設定',
			'explanation'  => 'ワークフローの設定をします。',
			'order'        => 10,
			'dependencies' => array()
		);

		return $retvals;
	}
}
