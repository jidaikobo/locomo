<?php
namespace Locomo;
class Actionset_Base extends Actionset
{
	/**
	 * create()
	 */
	public static function actionset_create($module, $obj, $id, $urls = array())
	{
		$actions = array(array($module."/create/", '新規作成'));
		$urls = static::generate_anchors($module, 'create', $actions, $obj, ['create']);

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '新規作成',
			'explanation'  => '新規作成権限',
			'order'        => 10,
			'dependencies' => array(
				'index',
				'view',
				'create',
			)
		);

		return $retvals;
	}

	/**
	 * view()
	 */
	public static function actionset_view($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array($module."/view/".$id, '閲覧'));
			$urls = static::generate_anchors($module, 'view', $actions, $obj, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '閲覧（通常項目）',
			'explanation'  => '通常項目の個票の閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				'view',
			)
		);
		return $retvals;
	}

	/**
	 * edit()
	 */
	public static function actionset_edit($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array($module."/edit/".$id, '編集'));
			$urls = static::generate_anchors($module, 'edit', $actions, $obj, ['edit','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '編集（通常項目）',
			'explanation'  => '通常項目の編集権限',
			'order'        => 10,
			'dependencies' => array(
				'view',
				'edit',
			)
		);
		return $retvals;
	}

	/**
	 * edit_anyway()
	 */
	public static function actionset_edit_anyway($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array($module."/edit/".$id, '編集'));
			$urls = static::generate_anchors($module, 'edit_anyway', $actions, $obj, ['edit','create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'id_segment'   => '',
			'action_name'  => '編集（すべての項目）',
			'explanation'  => 'すべての項目（ごみ箱、不可視、期限切れ等々）の編集権限',
			'order'        => 10,
			'dependencies' => array(
				'view',
				'view_anyway',
				'edit',
				'edit_anyway',
			)
		);
		return $retvals;
	}
		
	/**
	 * edit_deleted()
	 */
	public static function actionset_edit_deleted($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array($module."/edit/".$id, '編集'));
			$urls = static::generate_anchors($module, 'edit_deleted', $actions, $obj, ['edit','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '削除された項目の編集',
			'explanation'  => '削除された項目の編集権限です。削除された項目の閲覧権限も付与されます。',
			'order'        => 10,
			'dependencies' => array(
				'index_deleted',
				'view_deleted',
				'edit_deleted',
			)
		);
		return $retvals;
	}
	
	/**
	 * delete()
	 */
	public static function actionset_delete($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array($module."/delete/".$id, '削除', array('class' => 'confirm', 'data-text' => '削除してよいですか？')));
			$urls = static::generate_anchors($module, 'delete', $actions, $obj, ['create']);
		endif;

		//retval
		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '項目の削除',
			'explanation'  => '項目を削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 15,
			'dependencies' => array(
				'view',
				'view_deleted',
				'index_deleted',
				'delete',
				'confirm_delete',
			)
		);
		return $retvals;
	}

	/**
	 * undelete()
	 */
	public static function actionset_undelete($module, $obj, $id, $urls = array())
	{
		if(isset($obj->deleted_at) && $obj->deleted_at && $id):
			$actions = array(array($module."/undelete/".$id, '復活'));
			$urls = static::generate_anchors($module, 'undelete', $actions, $obj, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '項目の復活',
			'explanation'  => '削除された項目を復活する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 10,
			'dependencies' => array(
				'index',
				'view',
				'view_deleted',
				'index_deleted',
				'undelete',
			)
		);
		return $retvals;
	}


	/**
	 * delete_deleted()
	 */
	public static function actionset_delete_deleted($module, $obj, $id, $urls = array())
	{
		if(isset($obj->deleted_at) && $obj->deleted_at && $id):
			$actions = array(array($module."/delete_deleted/".$id, '完全削除'));
			$urls = static::generate_anchors($module, 'delete_deleted', $actions, $obj, ['create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '項目の完全な削除',
			'explanation'  => '削除された項目を復活できないように削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 10,
			'dependencies' => array(
				'view',
				'view_deleted',
				'index_deleted',
				'delete_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_deleted()
	 */
	public static function actionset_view_deleted($module, $obj, $id, $urls = array())
	{
		if(isset($obj->deleted_at) && $obj->deleted_at && $id):
			$actions = array(array($module."/view/".$id, '閲覧'));
			$urls = static::generate_anchors($module, 'view_deleted', $actions, $obj, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '閲覧（削除された項目）',
			'explanation'  => '削除された項目の閲覧権限です。削除権限、復活権限は別に設定する必要があります。',
			'order'        => 10,
			'dependencies' => array(
				'view_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_expired()
	 */
	public static function actionset_view_expired($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array($module."/view/".$id, '閲覧'));
			$urls = static::generate_anchors($module, 'view_expired', $actions, $obj, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '閲覧（期限切れ）',
			'explanation'  => '期限切れ項目の閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				'view_expired',
			)
		);
		return $retvals;
	}

	/**
	 * view_yet()
	 */
	public static function actionset_view_yet($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array($module."/view/".$id, '閲覧'));
			$urls = static::generate_anchors($module, 'view_yet', $actions, $obj, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '閲覧（予約項目）',
			'explanation'  => '予約項目の閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				'index_yet',
				'view_yet',
			)
		);
		return $retvals;
	}

	/**
	 * view_invisible()
	 */
	public static function actionset_view_invisible($module, $obj, $id, $urls = array())
	{
		if($id):
			$actions = array(array($module."/view/".$id, '閲覧'));
			$urls = static::generate_anchors($module, 'view_invisible', $actions, $obj, ['view','create']);
		endif;

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '閲覧（不可視項目）',
			'explanation'  => '不可視項目の閲覧権限',
			'order'        => 10,
			'dependencies' => array(
				'index_invisible',
				'view_invisible',
			)
		);
		return $retvals;
	}

}
