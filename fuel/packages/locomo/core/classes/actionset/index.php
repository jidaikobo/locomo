<?php
namespace Locomo;
class Actionset_Index extends Actionset
{
	/**
	 * index()
	 * @return  array
	 */
	public static function actionset_index($module, $obj, $get_authed_url)
	{
		if($get_authed_url):
			$url_str = $module."/index" ;
			$url = self::check_auth($module, 'index') ? $url_str : '' ;
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '一覧（通常項目）',
			'menu_str'     => '一覧',
			'explanation'  => '通常項目の一覧の閲覧権限です。',
			'dependencies' => array(
				'index',
			)
		);
		return $retvals;
	}

	/**
	 * index_admin()
	 * @return  array
	 */
	public static function actionset_index_admin($module, $obj, $get_authed_url)
	{
		$url_str = $module."/index_admin" ;
		$url = self::check_auth($module, 'index_admin') ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'action_name'  => '管理者向け一覧（通常項目）',
			'menu_str'     => '管理者向け一覧',
			'explanation'  => '通常項目の一覧（管理者向け）の閲覧権限です。管理者向けですが閲覧できるのは通常項目のみです。削除済み項目等は個別に権限を付与してください。',
			'dependencies' => array(
				'index_admin',
			)
		);
		return $retvals;
	}

	/**
	 * index_deleted()
	 * @return  array
	 */
	public static function actionset_index_deleted($module, $obj, $get_authed_url)
	{
		$url_str = $module."/index_deleted" ;
		$url = self::check_auth($module, 'index_deleted') ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'action_name'  => '一覧（削除された項目）',
			'menu_str'     => '削除項目一覧',
			'explanation'  => '削除された項目一覧の権限です。',
			'dependencies' => array(
				'index_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * index_expired()
	 * @return  array
	 */
	public static function actionset_index_expired($module, $obj, $get_authed_url)
	{
		$url_str = $module."/index_expired" ;
		$url = self::check_auth($module, 'index_expired') ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'action_name'  => '一覧（期限切れ項目）',
			'menu_str'     => '期限切れ項目一覧',
			'explanation'  => '期限切れ項目一覧の権限です。',
			'dependencies' => array(
				'index_expired',
			)
		);
		return $retvals;
	}

	/**
	 * index_yet()
	 * @return  array
	 */
	public static function actionset_index_yet($module, $obj, $get_authed_url)
	{
		$url = $module."/index_yet" ;
		$url = self::check_auth($module, 'index_yet') ? $url : '' ;

		$retvals = array(
			'url'          => $url,
			'action_name'  => '一覧（予約項目）',
			'menu_str'     => '予約項目一覧',
			'explanation'  => '予約項目一覧の権限です。',
			'dependencies' => array(
				'index_yet',
			)
		);
		return $retvals;
	}

	/**
	 * index_invisible()
	 * @return  array
	 */
	public static function actionset_index_invisible($module, $obj, $get_authed_url)
	{
		$url = $module."/index_invisible" ;
		$url = self::check_auth($module, 'index_invisible') ? $url: '';

		$retvals = array(
			'url'          => $url,
			'action_name'  => '一覧（不可視項目）',
			'menu_str'     => '不可視項目一覧',
			'explanation'  => '不可視項目一覧の権限です。',
			'dependencies' => array(
				'index_invisible',
			)
		);
		return $retvals;
	}

	public static function actionset_index_all($module, $obj, $get_authed_url)
	{
		$url_str = isset($obj->id) ? $module."/index_all" : null ;
		$url = self::check_auth($module, 'index_all') ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'action_name'  => '削除を含む全項目一覧',
			'menu_str'     => '全項目一覧',
			'explanation'  => '全項目項目一覧の権限です。',
			'dependencies' => array(
				'index',
				'index_admin',
				'index_deleted',
				'index_expired',
				'index_yet',
				'index_invisible',
				'index_all',
			)
		);
		return $retvals;
	}

	public static function actionset_index_revision($module, $obj, $get_authed_url)
	{
	$url = '';
/*
		$url_rev = $url ? "{$module}/options_revisions/postcategories" : '';
		$url = self::check_auth($module, 'index_revision') ? $url_str : '' ;
		$urls = array(
			array('カテゴリ設定', $url),
			array('カテゴリ設定履歴', $url_rev),
		);
*/
		$retvals = array(
			'url'          => $url,
			'action_name'  => 'リビジョン項目一覧',
			'menu_str'     => 'リビジョン一覧',
			'explanation'  => 'リビジョン項目一覧の権限です。',
			'dependencies' => array(
				'index_revision',
			)
		);
		return $retvals;
	}

}
