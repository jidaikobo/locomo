<?php
namespace Workflow;
class Actionset_Index_Workflowadmin extends \Actionset
{
	/**
	 * index_admin()
	 * @return  array
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."index_admin", '一覧'));
		$urls = static::generate_uris($controller, 'index_admin', $actions);

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフロー管理',
			'show_at_top'  => true,
			'explanation'  => 'ワークフロー管理。管理者のみアクセス可能。',
			'order'        => 10,
			'dependencies' => array()
		);
		return $retvals;
	}

	/**
	 * index_deleted()
	 */
	public static function actionset_index_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."index_deleted", 'ごみ箱'));
		$urls = static::generate_uris($controller, 'index_deleted', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（削除された項目）',
			'explanation'  => '削除された項目一覧の権限です。',
			'order'        => 20,
			'dependencies' => array(
				$controller.DS.'index_deleted',
			)
		);
		return $retvals;
	}
}
