<?php
namespace Locomo;
class Actionset_Owner extends Actionset
{
	/**
	 * view()
	 * @return  array
	 */
	public static function actionset_view($module, $obj, $get_authed_url)
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
	public static function actionset_view_revision($module, $obj, $get_authed_url)
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
	public static function actionset_view_expired($module, $obj, $get_authed_url)
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
	public static function actionset_view_yet($module, $obj, $get_authed_url)
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
	public static function actionset_view_deleted($module, $obj, $get_authed_url)
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
	public static function actionset_view_invisible($module, $obj, $get_authed_url)
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
	public static function actionset_edit($module, $obj, $get_authed_url)
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
	 * delete_file()
	 * @return  array
	 */
	public static function actionset_delete_file($module, $obj, $get_authed_url)
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
