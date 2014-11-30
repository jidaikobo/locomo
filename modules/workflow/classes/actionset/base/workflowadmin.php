<?php
namespace Workflow;
class Actionset_Base_Workflowadmin extends \Actionset
{
	/**
	 * view()
	 * @return  array
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'edit' && $id):
			$actions = array(array($controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($controller, 'view', $actions, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフロー閲覧',
			'explanation'  => 'ワークフローの閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				'view',
			)
		);
		return $retvals;
	}

	/**
	 * edit()
	 * @return  array
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'view' && $id):
			$actions = array(array($controller.DS."edit/".$id, '編集'));
			$urls = static::generate_uris($controller, 'edit', $actions, ['edit','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフロー名称の編集',
			'explanation'  => 'ワークフロー名称の編集権限',
			'order'        => 10,
			'dependencies' => array(
				'view',
				'edit',
			)
		);
		return $retvals;
	}
	
	/**
	 * create()
	 * @return  array
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."create", '新規作成'));
		$urls = static::generate_uris($controller, 'create', $actions, ['create']);

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '新規ワークフロー',
			'show_at_top'  => true,
			'explanation'  => '新規ワークフロー作成フォーム。管理者のみアクセス可能。',
			'order'        => 10,
			'dependencies' => array(
				'view',
				'create',
			)
		);

		return $retvals;
	}

	/**
	 * delete()
	 */
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		if ($id):
			$actions = array(array($controller.DS."delete/".$id, '削除', array('class' => 'confirm', 'data-jslcm-msg' => '削除してよいですか？')));
			$urls = static::generate_uris($controller, 'delete', $actions, ['create']);
		endif;

		//retval
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '項目の削除',
			'explanation'  => '項目を削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 40,
			'dependencies' => array(
				$controller.DS.'view',
				$controller.DS.'view_deleted',
				$controller.DS.'index_deleted',
				$controller.DS.'delete',
				$controller.DS.'confirm_delete',
			)
		);
		return $retvals;
	}

	/**
	 * undelete()
	 */
	public static function actionset_undelete($controller, $obj = null, $id = null, $urls = array())
	{
		if (isset($obj->deleted_at) && $obj->deleted_at && $id):
			$actions = array(array($controller.DS."undelete/".$id, '復活', array('class' => 'confirm', 'data-jslcm-msg' => '項目を復活してよいですか？')));
			$urls = static::generate_uris($controller, 'undelete', $actions, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '項目の復活',
			'explanation'  => '削除された項目を復活する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 50,
			'dependencies' => array(
				$controller.DS.'index',
				$controller.DS.'view',
				$controller.DS.'view_deleted',
				$controller.DS.'index_deleted',
				$controller.DS.'undelete',
			)
		);
		return $retvals;
	}

	/**
	 * delete_deleted()
	 */
	public static function actionset_delete_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		if (isset($obj->deleted_at) && $obj->deleted_at && $id):
			$actions = array(array($controller.DS."delete_deleted/".$id, '完全削除', array('class' => 'confirm', 'data-jslcm-msg' => '完全に削除してよいですか？')));
			$urls = static::generate_uris($controller, 'delete_deleted', $actions, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '項目の完全な削除',
			'explanation'  => '削除された項目を復活できないように削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 50,
			'dependencies' => array(
				$controller.DS.'view',
				$controller.DS.'view_deleted',
				$controller.DS.'index_deleted',
				$controller.DS.'delete_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_deleted()
	 */
	public static function actionset_view_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		if (isset($obj->deleted_at) && $obj->deleted_at && $id):
			$actions = array(array($controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($controller, 'view_deleted', $actions, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '閲覧（削除された項目）',
			'explanation'  => '削除された項目の閲覧権限です。削除権限、復活権限は別に設定する必要があります。',
			'order'        => 20,
			'dependencies' => array(
				$controller.DS.'view_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * setup()
	 * @return  array
	 */
	public static function actionset_setup($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'view' && $id):
			$actions = array(array($controller.DS."setup/".$id, '設定'));
			$urls = static::generate_uris($controller, 'setup', $actions, ['setup','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフローの設定',
			'explanation'  => 'ワークフローの設定。管理者のみアクセス可能。',
			'order'        => 10,
			'dependencies' => array()
		);

		return $retvals;
	}
}
