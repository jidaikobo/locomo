<?php
class Actionset_Adrs extends \Actionset_Base
{
	use \Actionset_Traits_Testdata;
	use \Actionset_Traits_Revision;
//	use \Actionset_Traits_Wrkflw;

	/**
	 * actionset_create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::create($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::view($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_edit()
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::edit($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_delete()
	 */
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::delete($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_undelete()
	 */
	public static function actionset_undelete($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::undelete($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_admin($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_index_deleted()
	 */
	public static function actionset_index_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_deleted($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_index_all()
	 */
	public static function actionset_index_all($controller, $obj = null, $id = null, $urls = array())
	{
		return \Actionset_Base::index_all($controller, $obj, $id, $urls);
	}

	/**
	 * actionset_edit_adrsgrp()
	 */
	public static function actionset_edit_adrsgrp($controller, $obj = null, $id = null, $urls = array())
	{
		$urls = array(
			array($controller.DS."edit_adrsgrp/", 'グループの設定'),
			array($controller.DS."edit_adrsgrp/?create=1", 'グループの新規作成'),
		);

		$retvals = array(
			'realm'        => 'option' ,
			'urls'         => $urls ,
			'action_name'  => 'グループの設定',
			'show_at_top'  => true,
			'explanation'  => 'アドレス帳のグループ設定です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.'/edit_adrsgrp',
			)
		);
		return $retvals;
	}
}
