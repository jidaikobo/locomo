<?php
namespace Kontiki;
class Actionset_Owner
{
	/**
	 * actionItems()
	 * @param str $controller
	 * @return  obj
	 */
	public static function actionItems($controller = null)
	{
		$actions = (object) array();
		$actions->view           = self::view();
		$actions->view_revision  = self::view_revision();
		$actions->view_expired   = self::view_expired();
		$actions->view_yet       = self::view_yet();
		$actions->view_deleted   = self::view_deleted();
		$actions->view_invisible = self::view_invisible();
		$actions->edit           = self::edit();
		$actions->edit_deleted   = self::edit_deleted();
		return $actions;
	}

	/**
	 * view()
	 * @return  array
	 */
	private static function view()
	{
		$retvals = array(
			'action_name' => '閲覧（通常項目）',
			'explanation' => '項目制作者への項目閲覧権限です。一覧の閲覧権限は許可されません。',
			'dependencies' => array(
				'view',
			)
		);
		return $retvals;
	}

	/**
	 * view_revision()
	 * @return  array
	 */
	private static function view_revision()
	{
		$retvals = array(
			'action_name' => '閲覧（リビジョン）',
			'explanation' => '作業履歴の閲覧権限です。この権限を許可すると、元の項目が不可視、予約、期限切れ、削除済み等の状態であっても、履歴はみることができるようになります。また、通常項目の編集権限も許可されます。',
			'dependencies' => array(
				'view',
				'edit',
				'view_revision',
			)
		);
		return $retvals;
	}
	
	/**
	 * view_expired()
	 * @return  array
	 */
	private static function view_expired()
	{
		$retvals = array(
			'action_name' => '閲覧（期限切れ）',
			'explanation' => '期限切れ項目の閲覧権限です。',
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
	private static function view_yet()
	{
		$retvals = array(
			'action_name' => '閲覧（予約項目）',
			'explanation' => '予約項目の閲覧権限です。',
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
	private static function view_deleted()
	{
		$retvals = array(
			'action_name' => '閲覧（削除された項目）',
			'explanation' => '削除された項目の閲覧権限です。削除権限、復活権限は別に設定する必要があります。',
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
	private static function view_invisible()
	{
		$retvals = array(
			'action_name' => '閲覧（不可視項目）',
			'explanation' => '不可視項目の閲覧権限',
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
	private static function edit()
	{
		$retvals = array(
			'action_name' => '項目の編集',
			'explanation' => '特殊な条件のない項目の編集権限',
			'dependencies' => array(
				'view',
				'edit',
			)
		);
		return $retvals;
	}

	/**
	 * edit_deleted()
	 * @return  array
	 */
	private static function edit_deleted()
	{
		$retvals = array(
			'action_name' => '削除された項目の編集',
			'explanation' => '削除された項目の編集権限です。削除された項目の閲覧権限も付与されます。',
			'dependencies' => array(
				'view_deleted',
				'edit_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * delete_file()
	 * @return  array
	 */
	private static function delete_file()
	{
		$retvals = array(
			'action_name' => 'ファイルの削除権限',
			'explanation' => '添付ファイルの削除権限です。',
			'dependencies' => array(
				'delete_file',
			)
		);
		return $retvals;
	}
}
