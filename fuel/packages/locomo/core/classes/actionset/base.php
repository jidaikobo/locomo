<?php
namespace Locomo;
class Actionset_Base extends Actionset
{
	/*
	(str)  menu_str      メニューで用いる
	(str)  url           メニューに表示するリンク先
	(bool) is_admin_only 管理者のみに許された行為。ACL設定画面に表示されなくなる
	(str)  action_name   ACL設定画面で用いる
	(str)  explanation   ACL設定画面で用いる説明文
	(arr)  dependencies  このアクションセットが依存するアクション
	*/

	/**
	 * create()
	 */
	public static function actionset_create($module, $obj, $get_authed_url)
	{
		if($get_authed_url):
			$url_str = $module."/create" ;
			$url = self::check_auth($module, 'create') ? $url_str : '' ;
		endif;

		//edit画面では出さない
//		$url = (strpos( \Uri::string(), 'edit' ) !== false) ? '' : $url;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '新規作成',
			'menu_str'     => '新規作成',
			'explanation'  => '新規作成権限',
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
	public static function actionset_view($module, $obj, $get_authed_url)
	{
		$pk_id = is_object($obj) && method_exists($obj, 'get_primary_keys') ? 
			$obj::get_primary_keys('first'):
			null;
		if($get_authed_url && $pk_id && ! in_array(\Request::main()->action, ['view','create'])):
			$url_str = isset($obj->$pk_id) ? $module."/view/".$obj->$pk_id : null ;
			$url = self::check_auth($module, 'view') ? $url_str : '' ;
			if( ! $url){
				$url = self::check_owner_auth($module, 'view', $obj) ? $url_str : '' ;
			}
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '閲覧（通常項目）',
			'menu_str'     => '閲覧',
			'explanation'  => '通常項目の個票の閲覧権限です。',
			'dependencies' => array(
				'view',
			)
		);
		return $retvals;
	}

	/**
	 * edit()
	 */
	public static function actionset_edit($module, $obj, $get_authed_url)
	{
		$pk_id = is_object($obj) && method_exists($obj, 'get_primary_keys') ? 
			$obj::get_primary_keys('first'):
			null;
		if($get_authed_url && $pk_id && ! in_array(\Request::main()->action, ['edit','create'])):
			$url_str = isset($obj->$pk_id) ? $module."/edit/".$obj->$pk_id : null ;
			$url = self::check_auth($module, 'edit') ? $url_str : '' ;
			if( ! $url){
				$url = self::check_owner_auth($module, 'edit', $obj) ? $url_str : '' ;
			}
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '編集（通常項目）',
			'menu_str'     => '編集',
			'explanation'  => '通常項目の編集権限',
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
	public static function actionset_edit_anyway($module, $obj, $get_authed_url)
	{
		$pk_id = is_object($obj) && method_exists($obj, 'get_primary_keys') ? 
			$obj::get_primary_keys('first'):
			null;
		if($get_authed_url && $pk_id && ! in_array(\Request::main()->action, ['edit','create'])):
			$url_str = isset($obj->$pk_id) ? $module."/edit/".$obj->$pk_id : null ;
			$url = self::check_auth($module, 'edit_anyway') ? $url_str : '' ;
			if( ! $url){
				$url = self::check_owner_auth($module, 'edit_anyway', $obj) ? $url_str : '' ;
			}
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'id_segment'   => '',
			'action_name'  => '編集（すべての項目）',
			'menu_str'     => '編集',
			'explanation'  => 'すべての項目（ごみ箱、不可視、期限切れ等々）の編集権限',
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
	public static function actionset_edit_deleted($module, $obj, $get_authed_url)
	{
		$pk_id = is_object($obj) && method_exists($obj, 'get_primary_keys') ? 
			$obj::get_primary_keys('first'):
			null;
		if($get_authed_url && $pk_id && ! in_array(\Request::main()->action, ['edit','create'])):
			$url_str = isset($obj->$pk_id) ? $module."/edit/".$obj->$pk_id : null ;
			$url = self::check_auth($module, 'edit_deleted') ? $url_str : '' ;
			if( ! $url){
				$url = self::check_owner_auth($module, 'edit_deleted', $obj) ? $url_str : '' ;
			}
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '削除された項目の編集',
			'menu_str'     => '編集',
			'explanation'  => '削除された項目の編集権限です。削除された項目の閲覧権限も付与されます。',
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
	public static function actionset_delete($module, $obj, $get_authed_url)
	{
		if($get_authed_url && $obj && ! in_array(\Request::main()->action, ['create'])):
			//url
			$url_str = null ;
			if(isset($obj->deleted_at) && $obj->deleted_at == null):
				$url_str = isset($obj->id) ? $module."/delete/$obj->id" : null ;
			endif;
			$url = self::check_auth($module, 'delete') ? $url_str :'';
			if( ! $url){
				$url = self::check_owner_auth($module, 'delete', $obj) ? $url_str : '' ;
			}
		endif;

		//retval
		$retvals = array(
			'url'          => @$url ?: '' ,
			'confirm'      => true,
			'action_name'  => '項目の削除',
			'menu_str'     => '削除',
			'explanation'  => '項目を削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
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
	public static function actionset_undelete($module, $obj, $get_authed_url)
	{
		if($get_authed_url):
			$url_str = null ;
			if(isset($obj->deleted_at) && $obj->deleted_at):
				$url_str = isset($obj->id) ? $module."/undelete/$obj->id" : null ;
			endif;
			$url = self::check_auth($module, 'undelete') ? $url_str : '' ;
			if( ! $url){
				$url = self::check_owner_auth($module, 'undelete', $obj) ? $url_str : '' ;
			}
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '項目の復活',
			'menu_str'     => '復活',
			'explanation'  => '削除された項目を復活する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
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
	public static function actionset_delete_deleted($module, $obj, $get_authed_url)
	{
		if($get_authed_url):
			$url_str = null ;
			if(isset($obj->deleted_at) && $obj->deleted_at):
				$url_str = isset($obj->id) ? $module."/delete_deleted/$obj->id" : null ;
			endif;
			$url = self::check_auth($module, 'delete_deleted') ? $url_str : '' ;
			if( ! $url){
				$url = self::check_owner_auth($module, 'delete_deleted', $obj) ? $url_str : '' ;
			}
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '項目の完全な削除',
			'menu_str'     => '完全に削除',
			'explanation'  => '削除された項目を復活できないように削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
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
	public static function actionset_view_deleted($module, $obj, $get_authed_url)
	{
		$pk_id = is_object($obj) && method_exists($obj, 'get_primary_keys') ? 
			$obj::get_primary_keys('first'):
			null;
		if($get_authed_url && $pk_id && ! in_array(\Request::main()->action, ['view','create'])):
			$url_str = isset($obj->$pk_id) ? $module."/view/".$obj->$pk_id : null ;
			$url = self::check_auth($module, 'view_deleted') ? $url_str : '' ;
			if( ! $url){
				$url = self::check_owner_auth($module, 'view_deleted', $obj) ? $url_str : '' ;
			}
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '閲覧（削除された項目）',
			'menu_str'     => '閲覧',
			'explanation'  => '削除された項目の閲覧権限です。削除権限、復活権限は別に設定する必要があります。',
			'dependencies' => array(
				'view_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_expired()
	 */
	public static function actionset_view_expired($module, $obj, $get_authed_url)
	{
		$pk_id = is_object($obj) && method_exists($obj, 'get_primary_keys') ? 
			$obj::get_primary_keys('first'):
			null;
		if($get_authed_url && $pk_id && ! in_array(\Request::main()->action, ['view','create'])):
			$url_str = isset($obj->$pk_id) ? $module."/view/".$obj->$pk_id : null ;
			$url = self::check_auth($module, 'view_expired') ? $url_str : '' ;
			if( ! $url){
				$url = self::check_owner_auth($module, 'view_expired', $obj) ? $url_str : '' ;
			}
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '閲覧（期限切れ）',
			'menu_str'     => '閲覧',
			'explanation'  => '期限切れ項目の閲覧権限です。',
			'dependencies' => array(
				'view_expired',
			)
		);
		return $retvals;
	}

	/**
	 * view_yet()
	 */
	public static function actionset_view_yet($module, $obj, $get_authed_url)
	{
		$pk_id = is_object($obj) && method_exists($obj, 'get_primary_keys') ? 
			$obj::get_primary_keys('first'):
			null;
		if($get_authed_url && $pk_id && ! in_array(\Request::main()->action, ['view','create'])):
			$url_str = isset($obj->$pk_id) ? $module."/view/".$obj->$pk_id : null ;
			$url = self::check_auth($module, 'view_yet') ? $url_str : '' ;
			if( ! $url){
				$url = self::check_owner_auth($module, 'view_yet', $obj) ? $url_str : '' ;
			}
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '閲覧（予約項目）',
			'menu_str'     => '閲覧',
			'explanation'  => '予約項目の閲覧権限です。',
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
	public static function actionset_view_invisible($module, $obj, $get_authed_url)
	{
		$pk_id = is_object($obj) && method_exists($obj, 'get_primary_keys') ? 
			$obj::get_primary_keys('first'):
			null;
		if($get_authed_url && $pk_id && ! in_array(\Request::main()->action, ['view','create'])):
			$url_str = isset($obj->$pk_id) ? $module."/view/".$obj->$pk_id : null ;
			$url = self::check_auth($module, 'view_invisible') ? $url_str : '' ;
			if( ! $url){
				$url = self::check_owner_auth($module, 'view_invisible', $obj) ? $url_str : '' ;
			}
		endif;

		$retvals = array(
			'url'          => @$url ?: '' ,
			'action_name'  => '閲覧（不可視項目）',
			'menu_str'     => '閲覧',
			'explanation'  => '不可視項目の閲覧権限',
			'dependencies' => array(
				'index_invisible',
				'view_invisible',
			)
		);
		return $retvals;
	}

	/**
	 * add_testdata()
	 */
	public static function _actionset_add_testdata($module, $obj, $get_authed_url)
	{
		$url = '';
		$usergroup_ids = \User\Controller_User::$userinfo['usergroup_ids'];

		//ルート管理者のみ
		if(in_array(-2, $usergroup_ids)):
			$url = $module."/add_testdata";
		endif;

		//インデクスでしか表示しない
		$url = (substr(\Uri::string(), -12) == '/index_admin') ? $url : '';

		$retvals = array(
			'is_admin_only' => true,
			'url'           => $url,
			'id_segment'    => null,
			'confirm'       => true,
			'action_name'   => 'テストデータの追加',
			'menu_str'      => 'テストデータ追加',
			'explanation'   => '開発者向けメニュー。テストデータの追加です。',
			'dependencies'  => array(
				'add_testdata',
			)
		);
		return $retvals;
	}

}
