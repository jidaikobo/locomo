<?php
namespace Locomo;
class Actionset_Index extends Actionset
{
	/**
	 * index()
	 */
	public static function actionset_index($controller, $module, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($module.DS.$controller.DS."index", '一覧'));
		$urls = static::generate_uris($module, $controller, 'index', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（通常項目）',
			'explanation'  => '通常項目の一覧の閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				$module.DS.$controller.DS.'index',
			)
		);
		return $retvals;
	}

	/**
	 * index_admin()
	 */
	public static function actionset_index_admin($controller, $module, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($module.DS.$controller.DS."index_admin", '一覧'));
		$urls = static::generate_uris($module, $controller, 'index_admin', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '管理者向け一覧（通常項目）',
			'explanation'  => '通常項目の一覧（管理者向け）の閲覧権限です。管理者向けですが閲覧できるのは通常項目のみです。削除済み項目等は個別に権限を付与してください。',
			'order'        => 10,
			'dependencies' => array(
				$module.DS.$controller.DS.'index_admin',
			)
		);
		return $retvals;
	}

	/**
	 * index_deleted()
	 */
	public static function actionset_index_deleted($controller, $module, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($module.DS.$controller.DS."index_deleted", 'ごみ箱'));
		$urls = static::generate_uris($module, $controller, 'index_deleted', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（削除された項目）',
			'explanation'  => '削除された項目一覧の権限です。',
			'order'        => 10,
			'dependencies' => array(
				$module.DS.$controller.DS.'index_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * index_expired()
	 */
	public static function actionset_index_expired($controller, $module, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($module.DS.$controller.DS."index_expired", '期限切れ項目'));
		$urls = static::generate_uris($module, $controller, 'index_expired', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（期限切れ項目）',
			'explanation'  => '期限切れ項目一覧の権限です。',
			'order'        => 10,
			'dependencies' => array(
				$module.DS.$controller.DS.'index_expired',
			)
		);
		return $retvals;
	}

	/**
	 * index_yet()
	 */
	public static function actionset_index_yet($controller, $module, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($module.DS.$controller.DS."index_yet", '予約項目'));
		$urls = static::generate_uris($module, $controller, 'index_yet', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（予約項目）',
			'explanation'  => '予約項目一覧の権限です。',
			'order'        => 10,
			'dependencies' => array(
				$module.DS.$controller.DS.'index_yet',
			)
		);
		return $retvals;
	}

	/**
	 * index_invisible()
	 */
	public static function actionset_index_invisible($controller, $module, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($module.DS.$controller.DS."index_invisible", '不可視項目'));
		$urls = static::generate_uris($module, $controller, 'index_invisible', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（不可視項目）',
			'explanation'  => '不可視項目一覧の権限です。',
			'order'        => 10,
			'dependencies' => array(
				$module.DS.$controller.DS.'index_invisible',
			)
		);
		return $retvals;
	}

	public static function actionset_index_all($controller, $module, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($module.DS.$controller.DS."index_all", 'すべて'));
		$urls = static::generate_uris($module, $controller, 'index_all', $actions);

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '削除を含む全項目一覧',
			'explanation'  => '全項目項目一覧の権限です。',
			'order'        => 10,
			'dependencies' => array(
				$module.DS.$controller.DS.'index',
				$module.DS.$controller.DS.'index_admin',
				$module.DS.$controller.DS.'index_deleted',
				$module.DS.$controller.DS.'index_expired',
				$module.DS.$controller.DS.'index_yet',
				$module.DS.$controller.DS.'index_invisible',
				$module.DS.$controller.DS.'index_all',
			)
		);
		return $retvals;
	}
}
