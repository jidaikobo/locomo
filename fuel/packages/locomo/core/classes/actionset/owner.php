<?php
namespace Locomo;
class Actionset_Owner extends Actionset
{
	/**
	 * view()
	 * @return  array
	 */
	public static function actionset_view($module, $obj, $id, $urls = array())
	{
		$retvals = array(
			'action_name' => '閲覧（通常項目）',
			'explanation' => '項目制作者への項目閲覧権限です。一覧の閲覧権限は許可されません。',
			'order'        => 10,
			'dependencies' => array(
				'view',
			)
		);
		return $retvals;
	}

	/**
	 * view_expired()
	 * @return  array
	 */
	public static function actionset_view_expired($module, $obj, $id, $urls = array())
	{
		$retvals = array(
			'action_name' => '閲覧（期限切れ）',
			'explanation' => '期限切れ項目の閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				'view_expired',
			)
		);
		return $retvals;
	}

	/**
	 * view_yet()
	 * @return  array
	 */
	public static function actionset_view_yet($module, $obj, $id, $urls = array())
	{
		$retvals = array(
			'action_name' => '閲覧（予約項目）',
			'explanation' => '予約項目の閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				'view_yet',
			)
		);
		return $retvals;
	}

	/**
	 * view_deleted()
	 * @return  array
	 */
	public static function actionset_view_deleted($module, $obj, $id, $urls = array())
	{
		$retvals = array(
			'action_name' => '閲覧（削除された項目）',
			'explanation' => '削除された項目の閲覧権限です。削除権限、復活権限は別に設定する必要があります。',
			'order'        => 10,
			'dependencies' => array(
				'index_deleted',
				'view_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_invisible()
	 * @return  array
	 */
	public static function actionset_view_invisible($module, $obj, $id, $urls = array())
	{
		$retvals = array(
			'action_name' => '閲覧（不可視項目）',
			'explanation' => '不可視項目の閲覧権限',
			'order'        => 10,
			'dependencies' => array(
				'view_invisible',
			)
		);
		return $retvals;
	}

	/**
	 * edit()
	 * @return  array
	 */
	public static function actionset_edit($module, $obj, $id, $urls = array())
	{
		$retvals = array(
			'action_name' => '項目の編集',
			'explanation' => '特殊な条件のない項目の編集権限',
			'order'        => 10,
			'dependencies' => array(
				'view',
				'edit',
			)
		);
		return $retvals;
	}

	/**
	 * delete_file()
	 * @return  array
	 */
	public static function _actionset_delete_file($module, $obj, $id, $urls = array())
	{
		$retvals = array(
			'action_name' => 'ファイルの削除権限',
			'explanation' => '添付ファイルの削除権限です。',
			'order'        => 10,
			'dependencies' => array(
				'delete_file',
			)
		);
		return $retvals;
	}
}
