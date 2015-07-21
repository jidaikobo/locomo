<?php
class Actionset_Msgbrd extends \Actionset_Base
{
//	use \Actionset_Traits_Revision;
//	use \Actionset_Traits_Wrkflw;
	use \Actionset_Traits_Testdata;

	/**
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_admin($controller, $obj, $id);
		$actions = array(
			'\Controller_Msgbrd/index_admin',
			'\Controller_Msgbrd/index_dashboard',
			'\Controller_Msgbrd/index_draft',
			'\Controller_Msgbrd/create',
			'\Controller_Msgbrd/view',
			'\Controller_Msgbrd/edit',
			'\Controller_Msgbrd/delete',
			'\Controller_Msgbrd/confirm_delete',
			'\Controller_Msgbrd/undelete',
			'\Controller_Msgbrd/view_deleted',
			'\Controller_Msgbrd/view_expired',
			'\Controller_Msgbrd/view_yet',
			'\Controller_Msgbrd/view_invisible',
			'\Controller_Msgbrd/index_deleted',
			'\Controller_Msgbrd/index_yet',
			'\Controller_Msgbrd/index_expired',
			'\Controller_Msgbrd/index_invisible',
			'\Controller_Msgbrd/index_all',
		);
		\Arr::set($retvals, 'dependencies', $actions);
		\Arr::set($retvals, 'action_name', 'メッセージボードへのアクセス権');
		return $retvals;
	}

	/**
	 * actionset_index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::index_admin($controller, $obj, $id);
	}

	/**
	 * actionset_index_draft()
	 */
	public static function actionset_index_draft($controller, $obj = null, $id = null, $urls = array())
	{
		static $count;
		$count = $count ?: \Model_Msgbrd::count(\Model_Msgbrd::set_draft_options());
		$urls = array(array($controller.DS."index_draft", "下書き ({$count})"));

		$retvals = array(
			'realm'        => 'index' ,
			'urls'         => $urls ,
			'action_name'  => '下書き',
			'show_at_top'  => true,
			'explanation'  => 'メッセージボードの下書きの一覧です。',
			'order'        => 11,
			'dependencies' => array(
				$controller.'/index_draft',
			)
		);
		return $retvals;
	}

	/**
	 * actionset_edit_categories()
	 */
	public static function actionset_edit_categories($controller, $obj = null, $id = null, $urls = array())
	{
		$urls = array(
			array($controller.DS."edit_categories/", 'カテゴリの設定'),
			array($controller.DS."edit_categories/?create=1", 'カテゴリの新規作成'),
		);

		$retvals = array(
			'realm'        => 'option' ,
			'urls'         => $urls ,
			'action_name'  => 'カテゴリの設定',
			'show_at_top'  => true,
			'explanation'  => 'メッセージボードのカテゴリ設定です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.'/edit_categories',
			)
		);
		return $retvals;
	}

	/**
	 * use parents
	 */

	// actionset_create
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::create($controller, $obj, $id, $urls);
	}

	// actionset_view
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::view($controller, $obj, $id, $urls);
	}

	// actionset_edit
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::edit($controller, $obj, $id, $urls);
	}

	// actionset_delete
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::delete($controller, $obj, $id, $urls);
	}

	// actionset_undelete
	public static function actionset_undelete($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::undelete($controller, $obj, $id, $urls);
	}

	// actionset_view_deleted
	public static function actionset_view_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::view_deleted($controller, $obj, $id, $urls);
	}

	// actionset_view_expired
	public static function actionset_view_expired($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::view_expired($controller, $obj, $id, $urls);
	}

	// actionset_view_yet
	public static function actionset_view_yet($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::view_yet($controller, $obj, $id, $urls);
	}

	// actionset_view_invisible
	public static function actionset_view_invisible($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::view_invisible($controller, $obj, $id, $urls);
	}

	// actionset_index_deleted
	public static function actionset_index_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::index_deleted($controller, $obj, $id, $urls);
	}

	// actionset_index_yet
	public static function actionset_index_yet($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::index_yet($controller, $obj, $id, $urls);
	}

	// actionset_index_expired
	public static function actionset_index_expired($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::index_expired($controller, $obj, $id, $urls);
	}

	// actionset_index_all
	public static function actionset_index_all($controller, $obj = null, $id = null, $urls = array())
	{
		return parent::index_all($controller, $obj, $id, $urls);
	}
}
