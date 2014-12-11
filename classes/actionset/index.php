<?php
namespace Locomo;
class Actionset_Index extends Actionset
{
	/**
	 * index()
	 */
	public static function actionset_index($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."index", '公開一覧'));
		$urls = static::generate_uris($controller, 'index', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（通常項目）',
			'explanation'  => '通常項目の一覧の閲覧権限です。',
			'acl_exp'      => '通常項目の一覧の閲覧権限です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.DS.'index',
			)
		);
		return $retvals;
	}

	/**
	 * index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."index_admin", '管理一覧'));
		$urls = static::generate_uris($controller, 'index_admin', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '管理者向け一覧（通常項目）',
			'show_at_top'  => false,
			'explanation'  => '通常項目の一覧（管理者向け）の閲覧権限です。管理者向けですが閲覧できるのは通常項目のみです。削除済み項目等は個別に権限を付与してください。',
			'acl_exp'      => '通常項目の一覧（管理者向け）の閲覧権限です。管理者向けですが閲覧できるのは通常項目のみです。削除済み項目等は個別に権限を付与してください。',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'index_admin',
			)
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
			'acl_exp'      => '削除された項目一覧の権限です。',
			'order'        => 20,
			'dependencies' => array(
				$controller.DS.'index_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * index_yet()
	 */
	public static function actionset_index_yet($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."index_yet", '予約項目'));
		$urls = static::generate_uris($controller, 'index_yet', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（予約項目）',
			'explanation'  => '予約項目一覧の権限です。',
			'acl_exp'      => '予約項目一覧の権限です。',
			'order'        => 30,
			'dependencies' => array(
				$controller.DS.'index_yet',
			)
		);
		return $retvals;
	}

	/**
	 * index_expired()
	 */
	public static function actionset_index_expired($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."index_expired", '期限切れ項目'));
		$urls = static::generate_uris($controller, 'index_expired', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（期限切れ項目）',
			'explanation'  => '期限切れ項目一覧の権限です。',
			'acl_exp'      => '期限切れ項目一覧の権限です。',
			'order'        => 40,
			'dependencies' => array(
				$controller.DS.'index_expired',
			)
		);
		return $retvals;
	}

	/**
	 * index_invisible()
	 */
	public static function actionset_index_invisible($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."index_invisible", '不可視項目'));
		$urls = static::generate_uris($controller, 'index_invisible', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（不可視項目）',
			'explanation'  => '不可視項目一覧の権限です。',
			'acl_exp'      => '不可視項目一覧の権限です。',
			'order'        => 50,
			'dependencies' => array(
				$controller.DS.'index_invisible',
			)
		);
		return $retvals;
	}

	/**
	 * index_all()
	 * 開発中。一旦停止。
	 */
	public static function _actionset_index_all($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."index_all", 'すべて'));
		$urls = static::generate_uris($controller, 'index_all', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '削除を含む全項目一覧',
			'explanation'  => '全項目項目一覧の権限です。',
			'acl_exp'      => '全項目項目一覧の権限です。',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'index',
				$controller.DS.'index_admin',
				$controller.DS.'index_deleted',
				$controller.DS.'index_expired',
				$controller.DS.'index_yet',
				$controller.DS.'index_invisible',
				$controller.DS.'index_all',
			)
		);
		return $retvals;
	}
}
