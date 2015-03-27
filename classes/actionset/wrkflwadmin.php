<?php
namespace Locomo;
class Actionset_Wrkflwadmin extends \Actionset
{
	/**
	 * view()
	 * @return  array
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'edit' && $id)
		{
			$urls = array(array($controller.DS."view/".$id, '閲覧'));
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフロー閲覧',
			'explanation'  => 'ワークフローを閲覧します。',
			'acl_exp'      => 'ワークフローの閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				$controller.'/view',
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
		if (\Request::main()->action == 'view' && $id)
		{
			$urls = array(array($controller.DS."edit/".$id, '編集'));
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフロー名称の編集',
			'explanation'  => 'ワークフロー名称の編集',
			'acl_exp'      => 'ワークフロー名称の編集権限',
			'order'        => 10,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/edit',
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
		if (\Request::main()->action != 'create')
		{
			$urls = array(array($controller.DS."create", '新規作成'));
		}

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '新規ワークフロー',
			'show_at_top'  => true,
			'explanation'  => '新規ワークフローを作成します。',
			'acl_exp'      => '新規ワークフロー作成フォーム。管理者のみアクセス可能。',
			'order'        => 10,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/create',
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
			$urls = array(array($controller.DS."delete/".$id, '削除', array('class' => 'confirm', 'data-jslcm-msg' => '削除してよいですか？')));
		endif;

		//retval
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '項目の削除',
			'explanation'  => '項目を削除します。',
			'acl_exp'      => '項目を削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 40,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/view_deleted',
				$controller.'/index_deleted',
				$controller.'/delete',
				$controller.'/confirm_delete',
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
			$urls = array(array($controller.DS."undelete/".$id, '復活', array('class' => 'confirm', 'data-jslcm-msg' => '項目を復活してよいですか？')));
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '項目の復活',
			'explanation'  => '削除された項目を復活します。',
			'acl_exp'      => '削除された項目を復活する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 50,
			'dependencies' => array(
				$controller.DS.'index',
				$controller.'/view',
				$controller.'/view_deleted',
				$controller.'/index_deleted',
				$controller.'/undelete',
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
			$urls = array(array($controller.DS."delete_deleted/".$id, '完全削除', array('class' => 'confirm', 'data-jslcm-msg' => '完全に削除してよいですか？')));
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '項目の完全な削除',
			'explanation'  => '削除された項目を復活できないように削除します。',
			'acl_exp'      => '削除された項目を復活できないように削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 50,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/view_deleted',
				$controller.'/index_deleted',
				$controller.'/delete_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_deleted()
	 */
	public static function actionset_view_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action != 'view' && isset($obj->deleted_at) && $obj->deleted_at && $id):
			$urls = array(array($controller.DS."view/".$id, '閲覧'));
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '閲覧（削除された項目）',
			'explanation'  => '削除された項目の閲覧します。',
			'acl_exp'      => '削除された項目の閲覧権限です。削除権限、復活権限は別に設定する必要があります。',
			'order'        => 20,
			'dependencies' => array(
				$controller.'/view_deleted',
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
			$urls = array(array($controller.DS."setup/".$id, '設定'));
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'ワークフローの設定',
			'explanation'  => 'ワークフローの設定をします。',
			'acl_exp'      => 'ワークフローの設定。管理者のみアクセス可能。',
			'order'        => 10,
			'dependencies' => array()
		);

		return $retvals;
	}

	/**
	 * index_admin()
	 * @return  array
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$urls = array(array($controller.DS."index_admin", '一覧'));

		$retvals = array(
			'realm'        => 'index' ,
			'urls'         => $urls ,
			'action_name'  => 'ワークフロー管理',
			'show_at_top'  => true,
			'explanation'  => 'ワークフロー管理。',
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
		$urls = array(array($controller.DS."index_deleted", 'ごみ箱'));

		$retvals = array(
			'realm'        => 'index' ,
			'urls'          => $urls ,
			'action_name'  => '一覧（削除された項目）',
			'explanation'  => '削除された項目一覧です。',
			'acl_exp'      => '削除された項目一覧の権限です。',
			'order'        => 20,
			'dependencies' => array(
				$controller.'/index_deleted',
			)
		);
		return $retvals;
	}
}
