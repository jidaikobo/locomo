<?php
namespace Locomo;
class Actionset_Index extends Actionset
{
	/**
	 * index()
	 */
	public static function actionset_index($module, $obj, $id, $urls = array())
	{
//		$url_str = $module."/index" ;
//		$urls = \Auth::auth($url_str) ? array(\Html::anchor($url_str,'一覧')) : '' ;

		$retvals = array(
//			'urls'          => $urls ,
			'action_name'  => '一覧（通常項目）',
			'explanation'  => '通常項目の一覧の閲覧権限です。',
			'order'        => 10,
			'dependencies' => array(
				'index',
			)
		);
		return $retvals;
	}

	/**
	 * index_admin()
	 */
	public static function actionset_index_admin($module, $obj, $id, $urls = array())
	{
		$url_str = $module."/index_admin" ;
		$urls = \Auth::auth($url_str) ? array(\Html::anchor($url_str,'一覧')) : '' ;

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '管理者向け一覧（通常項目）',
			'explanation'  => '通常項目の一覧（管理者向け）の閲覧権限です。管理者向けですが閲覧できるのは通常項目のみです。削除済み項目等は個別に権限を付与してください。',
			'order'        => 10,
			'dependencies' => array(
				'index_admin',
			)
		);
		return $retvals;
	}

	/**
	 * index_deleted()
	 */
	public static function actionset_index_deleted($module, $obj, $id, $urls = array())
	{
		$url_str = $module."/index_deleted" ;
		$urls = \Auth::auth($url_str) ? array(\Html::anchor($url_str,'削除項目')) : '' ;

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（削除された項目）',
			'explanation'  => '削除された項目一覧の権限です。',
			'order'        => 10,
			'dependencies' => array(
				'index_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * index_expired()
	 */
	public static function actionset_index_expired($module, $obj, $id, $urls = array())
	{
		$url_str = $module."/index_expired" ;
		$urls = \Auth::auth($url_str) ? array(\Html::anchor($url_str,'期限切れ項目')) : '' ;

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（期限切れ項目）',
			'explanation'  => '期限切れ項目一覧の権限です。',
			'order'        => 10,
			'dependencies' => array(
				'index_expired',
			)
		);
		return $retvals;
	}

	/**
	 * index_yet()
	 */
	public static function actionset_index_yet($module, $obj, $id, $urls = array())
	{
		$url_str = $module."/index_yet" ;
		$urls = \Auth::auth($url_str) ? array(\Html::anchor($url_str,'予約項目')) : '' ;

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（予約項目）',
			'explanation'  => '予約項目一覧の権限です。',
			'order'        => 10,
			'dependencies' => array(
				'index_yet',
			)
		);
		return $retvals;
	}

	/**
	 * index_invisible()
	 */
	public static function actionset_index_invisible($module, $obj, $id, $urls = array())
	{
		$url_str = $module."/index_invisible" ;
		$urls = \Auth::auth($url_str) ? array(\Html::anchor($url_str,'不可視項目')) : '' ;

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '一覧（不可視項目）',
			'explanation'  => '不可視項目一覧の権限です。',
			'order'        => 10,
			'dependencies' => array(
				'index_invisible',
			)
		);
		return $retvals;
	}

	public static function actionset_index_all($module, $obj, $id, $urls = array())
	{
		$url_str = $module."/index_all" ;
		$urls = \Auth::auth($url_str) ? array(\Html::anchor($url_str,'すべて')) : '' ;

		$retvals = array(
			'urls'          => $urls ,
			'action_name'  => '削除を含む全項目一覧',
			'explanation'  => '全項目項目一覧の権限です。',
			'order'        => 10,
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
}
