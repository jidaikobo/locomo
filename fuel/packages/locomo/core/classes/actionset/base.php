<?php
namespace Locomo;
class Actionset_Base extends Actionset
{
	/**
	 * create()
	 */
	public static function actionset_create($controller, $module, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($module.DS.$controller.DS."create", '新規作成'));
		$urls = static::generate_uris($module, $controller, 'create', $actions, ['create']);

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '新規作成',
			'explanation'  => '新規作成権限',
			'order'        => 10,
			'dependencies' => array(
				$module.DS.$controller.DS.'index',
				$module.DS.$controller.DS.'view',
				$module.DS.$controller.DS.'create',
			)
		);

		return $retvals;
	}

	/**
	 * view()
	 */
	public static function actionset_view($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'edit' && $id):
			$actions = array(array($module.DS.$controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($module, $controller, 'view', $actions, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '閲覧（通常項目）',
			'explanation'  => '通常項目の個票の閲覧権限です。',
			'order'        => 20,
			'dependencies' => array(
				$module.DS.$controller.DS.'view',
			)
		);
		return $retvals;
	}

	/**
	 * edit()
	 */
	public static function actionset_edit($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'view' && $id):
			$actions = array(array($module.DS.$controller.DS."edit/".$id, '編集'));
			$urls = static::generate_uris($module, $controller, 'edit', $actions, ['edit','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '編集（通常項目）',
			'explanation'  => '通常項目の編集権限',
			'order'        => 30,
			'dependencies' => array(
				$module.DS.$controller.DS.'view',
				$module.DS.$controller.DS.'edit',
			)
		);
		return $retvals;
	}

	/**
	 * edit_anyway()
	 */
	public static function actionset_edit_anyway($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'view' && $id):
			$actions = array(array($module.DS.$controller.DS."edit/".$id, '編集'));
			$urls = static::generate_uris($module, $controller, 'edit_anyway', $actions, ['edit','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'id_segment'   => '',
			'action_name'  => '編集（すべての項目）',
			'explanation'  => 'すべての項目（ごみ箱、不可視、期限切れ等々）の編集権限',
			'order'        => 30,
			'dependencies' => array(
				$module.DS.$controller.DS.'view',
				$module.DS.$controller.DS.'view_anyway',
				$module.DS.$controller.DS.'edit',
				$module.DS.$controller.DS.'edit_anyway',
			)
		);
		return $retvals;
	}
		
	/**
	 * edit_deleted()
	 */
	public static function actionset_edit_deleted($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if(\Request::main()->action == 'view' && $id):
			$actions = array(array($module.DS.$controller.DS."edit/".$id, '編集'));
			$urls = static::generate_uris($module, $controller, 'edit_deleted', $actions, ['edit','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '削除された項目の編集',
			'explanation'  => '削除された項目の編集権限です。削除された項目の閲覧権限も付与されます。',
			'order'        => 30,
			'dependencies' => array(
				$module.DS.$controller.DS.'index_deleted',
				$module.DS.$controller.DS.'view_deleted',
				$module.DS.$controller.DS.'edit_deleted',
			)
		);
		return $retvals;
	}
	
	/**
	 * delete()
	 */
	public static function actionset_delete($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if($id):
			$actions = array(array($module.DS.$controller.DS."delete".$id, '削除', array('class' => 'confirm', 'data-jslcm-msg' => '削除してよいですか？')));
			$urls = static::generate_uris($module, $controller, 'delete', $actions, ['create']);
		endif;

		//retval
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '項目の削除',
			'explanation'  => '項目を削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 40,
			'dependencies' => array(
				$module.DS.$controller.DS.'view',
				$module.DS.$controller.DS.'view_deleted',
				$module.DS.$controller.DS.'index_deleted',
				$module.DS.$controller.DS.'delete',
				$module.DS.$controller.DS.'confirm_delete',
			)
		);
		return $retvals;
	}

	/**
	 * undelete()
	 */
	public static function actionset_undelete($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if(isset($obj->deleted_at) && $obj->deleted_at && $id):
			$actions = array(array($module.DS.$controller.DS."undelete".$id, '復活', array('class' => 'confirm', 'data-jslcm-msg' => '項目を復活してよいですか？')));
			$urls = static::generate_uris($module, $controller, 'undelete', $actions, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '項目の復活',
			'explanation'  => '削除された項目を復活する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 50,
			'dependencies' => array(
				$module.DS.$controller.DS.'index',
				$module.DS.$controller.DS.'view',
				$module.DS.$controller.DS.'view_deleted',
				$module.DS.$controller.DS.'index_deleted',
				$module.DS.$controller.DS.'undelete',
			)
		);
		return $retvals;
	}

	/**
	 * delete_deleted()
	 */
	public static function actionset_delete_deleted($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if(isset($obj->deleted_at) && $obj->deleted_at && $id):
			$actions = array(array($module.DS.$controller.DS."delete_deleted".$id, '完全削除', array('class' => 'confirm', 'data-jslcm-msg' => '完全に削除してよいですか？')));
			$urls = static::generate_uris($module, $controller, 'delete_deleted', $actions, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '項目の完全な削除',
			'explanation'  => '削除された項目を復活できないように削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 50,
			'dependencies' => array(
				$module.DS.$controller.DS.'view',
				$module.DS.$controller.DS.'view_deleted',
				$module.DS.$controller.DS.'index_deleted',
				$module.DS.$controller.DS.'delete_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_deleted()
	 */
	public static function actionset_view_deleted($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if(isset($obj->deleted_at) && $obj->deleted_at && $id):
			$actions = array(array($module.DS.$controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($module, $controller, 'view_deleted', $actions, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '閲覧（削除された項目）',
			'explanation'  => '削除された項目の閲覧権限です。削除権限、復活権限は別に設定する必要があります。',
			'order'        => 20,
			'dependencies' => array(
				$module.DS.$controller.DS.'view_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_expired()
	 */
	public static function actionset_view_expired($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if($id):
			$actions = array(array($module.DS.$controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($module, $controller, 'view_expired', $actions, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '閲覧（期限切れ）',
			'explanation'  => '期限切れ項目の閲覧権限です。',
			'order'        => 20,
			'dependencies' => array(
				$module.DS.$controller.DS.'view_expired',
			)
		);
		return $retvals;
	}

	/**
	 * view_yet()
	 */
	public static function actionset_view_yet($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if($id):
			$actions = array(array($module.DS.$controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($module, $controller, 'view_yet', $actions, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '閲覧（予約項目）',
			'explanation'  => '予約項目の閲覧権限です。',
			'order'        => 20,
			'dependencies' => array(
				$module.DS.$controller.DS.'index_yet',
				$module.DS.$controller.DS.'view_yet',
			)
		);
		return $retvals;
	}

	/**
	 * view_invisible()
	 */
	public static function actionset_view_invisible($controller, $module, $obj = null, $id = null, $urls = array())
	{
		if($id):
			$actions = array(array($module.DS.$controller.DS."view/".$id, '閲覧'));
			$urls = static::generate_uris($module, $controller, 'view_invisible', $actions, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '閲覧（不可視項目）',
			'explanation'  => '不可視項目の閲覧権限',
			'order'        => 20,
			'dependencies' => array(
				$module.DS.$controller.DS.'index_invisible',
				$module.DS.$controller.DS.'view_invisible',
			)
		);
		return $retvals;
	}

}
