<?php
class Actionset_Msgbrd extends \Actionset_Base
{
//	use \Actionset_Traits_Revision;
//	use \Actionset_Traits_Wrkflw;
//	use \Actionset_Traits_Testdata;

	/**
	 * actionset_index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		// urls
		$count = \Model_Msgbrd::count(array('where' => array('is_draft' => 0)));
		$urls = array(array($controller.DS."index_admin", "管理一覧 ({$count})"));

		$retvals = array(
			'realm'        => 'index',
			'urls'         => $urls ,
			'action_name'  => '管理者向け一覧（通常項目）',
			'show_at_top'  => false,
			'explanation'  => 'メッセージボードの投稿一覧です。',
			'acl_exp'      => 'メッセージボードの投稿一覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				$controller.'/index_admin',
			)
		);
		return $retvals;
	}

	/**
	 * actionset_index_draft()
	 */
	public static function actionset_index_draft($controller, $obj = null, $id = null, $urls = array())
	{
		$count = \Model_Msgbrd::count(array('where' => array('is_draft' => 1)));
		$urls = array(array($controller.DS."index_draft", "下書き ({$count})"));

		$retvals = array(
			'realm'        => 'index' ,
			'urls'         => $urls ,
			'action_name'  => '下書き',
			'show_at_top'  => true,
			'explanation'  => 'メッセージボードの下書きの一覧です。',
			'acl_exp'      => 'メッセージボードの下書きの一覧権限です。',
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
			'acl_exp'      => 'メッセージボードのカテゴリ設定権限です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.'/edit_categories',
			)
		);
		return $retvals;
	}
}
