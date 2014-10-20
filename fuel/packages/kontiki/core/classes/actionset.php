<?php
namespace Kontiki_Core;
class Actionset
{
	/**
	 * actionItems()
	 * @return  obj
	 */
	public static function actionItems($obj = null)
	{
		$module = \Util::get_module_name_from_class(get_called_class());

		$actions = (object) array();
		$actions->index            = self::index($module, $obj);
		$actions->view             = self::view($module, $obj);

		$actions->index_admin      = self::index_admin($module, $obj);
		$actions->index_all        = self::index_all($module, $obj);

		$actions->create           = self::create($module, $obj);
		$actions->edit             = self::edit($module, $obj);
		$actions->edit_anyway      = self::edit_anyway($module, $obj);
		$actions->delete           = self::delete($module, $obj);
		$actions->undelete         = self::undelete($module, $obj);
		$actions->delete_deleted   = self::delete_deleted($module, $obj);

		$actions->index_expired    = self::index_expired($module, $obj);
		$actions->view_expired     = self::view_expired($module, $obj);

		$actions->index_invisible  = self::index_invisible($module, $obj);
		$actions->view_invisible   = self::view_invisible($module, $obj);

		$actions->index_yet        = self::index_yet($module, $obj);
		$actions->view_yet         = self::view_yet($module, $obj);

		$actions->index_deleted    = self::index_deleted($module, $obj);
		$actions->view_deleted     = self::view_deleted($module, $obj);

		$actions->index_revision   = self::index_revision($module, $obj);

		$actions->add_testdata     = self::add_testdata($module, $obj);
		return $actions;
	}

	/**
	 * check_auth()
	 * @return  bool
	 */
	public static function check_auth($module, $action)
	{
		return \Acl\Controller_Acl::auth($module.'/'.$action, \User\Controller_User::$userinfo);
	}

	/**
	 * check_owner_auth()
	 * @return  bool
	 */
	public static function check_owner_auth($module, $action, $obj)
	{
		return \Acl\Controller_Acl::owner_auth($module, $action, $obj, \User\Controller_User::$userinfo) ;
	}

	/*
	(bool) is_admin_only 管理者のみに許された行為。ACL設定画面に表示されなくなる
	(bool) is_index      メニューに表示する際、インデクス系として表示する
	(str)  url           メニューに表示するリンク先
	(int)  id_segment    \Kontiki\Controller::set_current_id()で用いる。個票系の際は必要
	(str)  action_name   ACL設定画面で用いる
	(str)  explanation   ACL設定画面で用いる説明文
	(str)  menu_str      メニューで用いる
	(arr)  dependencies  このアクションセットが依存するアクション
	*/

	/**
	 * index()
	 * @return  array
	 */
	private static function index($module, $obj)
	{
		$url_str = "$module/index" ;
		$url = self::check_auth($module, 'index') ? $url_str : '' ;

		$retvals = array(
			'is_index'     => true,
			'url'          => $url,
			'id_segment'   => null,
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
	private static function index_admin($module, $obj)
	{
		$url_str = "$module/index_admin" ;
		$url = self::check_auth($module, 'index_admin') ? $url_str : '' ;

		$retvals = array(
			'is_index'     => true,
			'url'          => $url,
			'id_segment'   => null,
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
	 * view()
	 * @return  array
	 */
	private static function view($module, $obj)
	{
		$url_str = isset($obj->id) ? "$module/view/$obj->id" : null ;
		$url = self::check_auth($module, 'view') ? $url_str : '' ;
		$url = self::check_owner_auth($module, 'view', $obj) ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
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
	 * create()
	 * @return  array
	 */
	private static function create($module, $obj)
	{
		$url_str = "$module/create" ;
		$url = self::check_auth($module, 'create') ? $url_str : '' ;

		//edit画面では出さない
//		$url = (strpos( \Uri::string(), 'edit' ) !== false) ? '' : $url;

		$retvals = array(
			'url'          => $url,
			'id_segment'   => null,
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
	 * edit()
	 * @return  array
	 */
	private static function edit($module, $obj)
	{
		$url_str = isset($obj->id) ? "$module/edit/$obj->id" : null ;
		$url = self::check_auth($module, 'edit') ? $url_str : '' ;
		$url = self::check_owner_auth($module, 'edit', $obj) ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
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
	 * @return  array
	 */
	private static function edit_anyway($module, $obj)
	{
		$url_str = isset($obj->id) ? "$module/edit/$obj->id" : null ;
		$url = self::check_auth($module, 'edit_anyway') ? $url_str : '' ;
		$url = self::check_owner_auth($module, 'edit_anyway', $obj) ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
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
	 * @return  array
	 */
	private static function edit_deleted($module, $obj)
	{
		$url_str = isset($obj->id) ? "$module/edit/$obj->id" : null ;
		$url = self::check_auth($module, 'edit_deleted') ? $url_str : '' ;
		$url = self::check_owner_auth($module, 'edit_deleted', $obj) ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
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
	 * @return  array
	 */
	private static function delete($module, $obj)
	{
		//url
		$url_str = null ;
		if(isset($obj->deleted_at) && $obj->deleted_at == null):
			$url_str = isset($obj->id) ? "$module/delete/$obj->id" : null ;
		endif;
		$url = self::check_auth($module, 'delete', $obj) ? $url_str :'';
		$url = self::check_owner_auth($module, 'delete', $obj) ? $url_str : '' ;

		//retval
		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
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
	 * @return  array
	 */
	private static function undelete($module, $obj)
	{
		$url_str = null ;
		if(isset($obj->deleted_at) && $obj->deleted_at):
			$url_str = isset($obj->id) ? "$module/undelete/$obj->id" : null ;
		endif;
		$url = self::check_auth($module, 'undelete') ? $url_str : '' ;
		$url = self::check_owner_auth($module, 'undelete', $obj) ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
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
	 * @return  array
	 */
	private static function delete_deleted($module, $obj)
	{
		$url_str = null ;
		if(isset($obj->deleted_at) && $obj->deleted_at):
			$url_str = isset($obj->id) ? "$module/delete_deleted/$obj->id" : null ;
		endif;
		$url = self::check_auth($module, 'delete_deleted') ? $url_str : '' ;
		$url = self::check_owner_auth($module, 'delete_deleted', $obj) ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
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
	 * index_deleted()
	 * @return  array
	 */
	private static function index_deleted($module, $obj)
	{
		$url_str = "$module/index_deleted" ;
		$url = self::check_auth($module, 'index_deleted') ? $url_str : '' ;

		$retvals = array(
			'is_index'     => true,
			'url'          => $url,
			'id_segment'   => null,
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
	 * view_deleted()
	 * @return  array
	 */
	private static function view_deleted($module, $obj)
	{
		$url_str = isset($obj->id) ? "$module/view/$obj->id" : null ;
		$url = self::check_auth($module, 'view_deleted') ? $url_str : '' ;
		$url = self::check_owner_auth($module, 'view_deleted', $obj) ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
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
	 * index_expired()
	 * @return  array
	 */
	private static function index_expired($module, $obj)
	{
		$url_str = "$module/index_expired" ;
		$url = self::check_auth($module, 'index_expired') ? $url_str : '' ;

		$retvals = array(
			'is_index'     => true,
			'url'          => $url,
			'id_segment'   => null,
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
	 * view_expired()
	 * @return  array
	 */
	private static function view_expired($module, $obj)
	{
		$url_str = isset($obj->id) ? "$module/view/$obj->id" : null ;
		$url = self::check_auth($module, 'view_expired') ? $url_str : '' ;
		$url = self::check_owner_auth($module, 'view_expired', $obj) ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
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
	 * index_yet()
	 * @return  array
	 */
	private static function index_yet($module, $obj)
	{
		$url = "$module/index_yet" ;
		$url = self::check_auth($module, 'index_yet') ? $url : '' ;

		$retvals = array(
			'is_index'     => true,
			'url'          => $url,
			'id_segment'   => null,
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
	 * view_yet()
	 * @return  array
	 */
	private static function view_yet($module, $obj)
	{
		$url_str = isset($obj->id) ? "$module/view/$obj->id" : null ;
		$url = self::check_auth($module, 'view_yet') ? $url_str : '' ;
		$url = self::check_owner_auth($module, 'view_yet', $obj) ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
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
	 * index_invisible()
	 * @return  array
	 */
	private static function index_invisible($module, $obj)
	{
		$url = "$module/index_invisible" ;
		$url = self::check_auth($module, 'index_invisible', $obj) ? $url: '';

		$retvals = array(
			'is_index'     => true,
			'url'          => $url,
			'id_segment'   => null,
			'action_name'  => '一覧（不可視項目）',
			'menu_str'     => '不可視項目一覧',
			'explanation'  => '不可視項目一覧の権限です。',
			'dependencies' => array(
				'index_invisible',
			)
		);
		return $retvals;
	}

	/**
	 * view_invisible()
	 * @return  array
	 */
	private static function view_invisible($module, $obj)
	{
		$url_str = isset($obj->id) ? "$module/view/$obj->id" : null ;
		$url = self::check_auth($module, 'view_invisible') ? $url_str : '' ;
		$url = self::check_owner_auth($module, 'view_invisible', $obj) ? $url_str : '' ;

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
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
	 * @return  array
	 */
	private static function add_testdata($module, $obj)
	{
		$url = '';
		$usergroup_ids = \User\Controller_User::$userinfo['usergroup_ids'];

		//ルート管理者のみ
		if(in_array(-2, $usergroup_ids)):
			$url = "$module/add_testdata";
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

	private static function index_all($module, $obj)
	{
		$url_str = isset($obj->id) ? "$module/index_all" : null ;
		$url = self::check_auth($module, 'index_all') ? $url_str : '' ;

		$retvals = array(
			'is_index'     => true,
			'url'          => $url,
			'id_segment'   => null,
			'action_name'  => '削除を含む全項目一覧',
			'menu_str'     => '全項目一覧',
			'explanation'  => '全項目項目一覧の権限です。',
			'dependencies' => array(
				'index_all',
			)
		);
		return $retvals;
	}

	private static function index_revision($module, $obj)
	{
		$url = 'oya/index_revision';
		/*
		$url_rev = $url ? "{$module}/options_revisions/postcategories" : '';
		$urls = array(
			array('カテゴリ設定', $url),
			array('カテゴリ設定履歴', $url_rev),
		);
		 */

		$retvals = array(
			'is_index'     => true,
			'url'          => $url,
			'id_segment'   => null,
			'action_name'  => 'リビジョン項目一覧',
			'menu_str'     => 'リビジョン一覧',
			'explanation'  => 'リビジョン項目一覧の権限です。',
			'dependencies' => array(
				'index_yet',
			)
		);
		return $retvals;
	}

}
