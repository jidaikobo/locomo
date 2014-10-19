<?php
namespace Kontiki_Core;
class Actionset
{
	/**
	 * actionItems()
	 * @return  obj
	 */
	public static function actionItems($controller = null, $item = null)
	{
		$actions = (object) array();
		$actions->index            = self::index($controller, $item);
		$actions->view             = self::view($controller, $item);

		$actions->index_admin      = self::index_admin($controller, $item);
		$actions->index_all        = self::index_all($controller, $item);

		$actions->create           = self::create($controller, $item);
		$actions->edit             = self::edit($controller, $item);
		$actions->edit_anyway      = self::edit_anyway($controller, $item);
		$actions->delete           = self::delete($controller, $item);
		$actions->undelete         = self::undelete($controller, $item);
		$actions->delete_deleted   = self::delete_deleted($controller, $item);

		$actions->index_expired    = self::index_expired($controller, $item);
		$actions->view_expired     = self::view_expired($controller, $item);

		$actions->index_invisible  = self::index_invisible($controller, $item);
		$actions->view_invisible   = self::view_invisible($controller, $item);

		$actions->index_yet        = self::index_yet($controller, $item);
		$actions->view_yet         = self::view_yet($controller, $item);

		$actions->index_deleted    = self::index_deleted($controller, $item);
		$actions->view_deleted     = self::view_deleted($controller, $item);

		$actions->index_revision   = self::index_revision($controller, $item);
		$actions->revision_list    = self::revision_list($controller, $item);

		$actions->add_testdata     = self::add_testdata($controller, $item);
		return $actions;
	}

	/**
	 * check_auth()
	 * @return  bool
	 */
	public static function check_auth($controller, $action)
	{
		return \Acl\Controller_Acl::auth($controller.'/'.$action, \User\Controller_User::$userinfo);
	}

	/**
	 * check_owner_auth()
	 * @return  bool
	 */
	public static function check_owner_auth($controller, $action, $obj)
	{
		return \Acl\Controller_Acl::owner_auth($controller, $action, $obj, \User\Controller_User::$userinfo) ;
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
	private static function index($controller, $item)
	{
		$url_str = "$controller/index" ;
		$url = self::check_auth($controller, 'index') ? $url_str : '' ;

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
	private static function index_admin($controller, $item)
	{
		$url_str = "$controller/index_admin" ;
		$url = self::check_auth($controller, 'index_admin') ? $url_str : '' ;

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
	private static function view($controller, $item)
	{
		$url_str = isset($item->id) ? "$controller/view/$item->id" : null ;
		$url = self::check_auth($controller, 'view') ? $url_str : '' ;
		$url = self::check_owner_auth($controller, 'view', $item) ? $url_str : '' ;

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
	private static function create($controller, $item)
	{
		$url_str = "$controller/create" ;
		$url = self::check_auth($controller, 'create') ? $url_str : '' ;

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
	private static function edit($controller, $item)
	{
		$url_str = isset($item->id) ? "$controller/edit/$item->id" : null ;
		$url = self::check_auth($controller, 'edit') ? $url_str : '' ;
		$url = self::check_owner_auth($controller, 'edit', $item) ? $url_str : '' ;

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
	private static function edit_anyway($controller, $item)
	{
		$url_str = isset($item->id) ? "$controller/edit/$item->id" : null ;
		$url = self::check_auth($controller, 'edit_anyway') ? $url_str : '' ;
		$url = self::check_owner_auth($controller, 'edit_anyway', $item) ? $url_str : '' ;

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
	private static function edit_deleted($controller, $item)
	{
		$url_str = isset($item->id) ? "$controller/edit/$item->id" : null ;
		$url = self::check_auth($controller, 'edit_deleted') ? $url_str : '' ;
		$url = self::check_owner_auth($controller, 'edit_deleted', $item) ? $url_str : '' ;

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
	private static function delete($controller, $item)
	{
		//url
		$url_str = null ;
		if(isset($item->deleted_at) && $item->deleted_at == null):
			$url_str = isset($item->id) ? "$controller/delete/$item->id" : null ;
		endif;
		$url = self::check_auth($controller, 'delete', $item) ? $url_str :'';
		$url = self::check_owner_auth($controller, 'delete', $item) ? $url_str : '' ;

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
	private static function undelete($controller, $item)
	{
		$url_str = null ;
		if(isset($item->deleted_at) && $item->deleted_at):
			$url_str = isset($item->id) ? "$controller/undelete/$item->id" : null ;
		endif;
		$url = self::check_auth($controller, 'undelete') ? $url_str : '' ;
		$url = self::check_owner_auth($controller, 'undelete', $item) ? $url_str : '' ;

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
	private static function delete_deleted($controller, $item)
	{
		$url_str = null ;
		if(isset($item->deleted_at) && $item->deleted_at):
			$url_str = isset($item->id) ? "$controller/delete_deleted/$item->id" : null ;
		endif;
		$url = self::check_auth($controller, 'delete_deleted') ? $url_str : '' ;
		$url = self::check_owner_auth($controller, 'delete_deleted', $item) ? $url_str : '' ;

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
	private static function index_deleted($controller, $item)
	{
		$url_str = "$controller/index_deleted" ;
		$url = self::check_auth($controller, 'index_deleted') ? $url_str : '' ;

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
	private static function view_deleted($controller, $item)
	{
		$url_str = isset($item->id) ? "$controller/view/$item->id" : null ;
		$url = self::check_auth($controller, 'view_deleted') ? $url_str : '' ;
		$url = self::check_owner_auth($controller, 'view_deleted', $item) ? $url_str : '' ;

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
	private static function index_expired($controller, $item)
	{
		$url_str = "$controller/index_expired" ;
		$url = self::check_auth($controller, 'index_expired') ? $url_str : '' ;

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
	private static function view_expired($controller, $item)
	{
		$url_str = isset($item->id) ? "$controller/view/$item->id" : null ;
		$url = self::check_auth($controller, 'view_expired') ? $url_str : '' ;
		$url = self::check_owner_auth($controller, 'view_expired', $item) ? $url_str : '' ;

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
	private static function index_yet($controller, $item)
	{
		$url = "$controller/index_yet" ;
		$url = self::check_auth($controller, 'index_yet') ? $url : '' ;

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
	private static function view_yet($controller, $item)
	{
		$url_str = isset($item->id) ? "$controller/view/$item->id" : null ;
		$url = self::check_auth($controller, 'view_yet') ? $url_str : '' ;
		$url = self::check_owner_auth($controller, 'view_yet', $item) ? $url_str : '' ;

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
	private static function index_invisible($controller, $item)
	{
		$url = "$controller/index_invisible" ;
		$url = self::check_auth($controller, 'index_invisible', $item) ? $url: '';

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
	private static function view_invisible($controller, $item)
	{
		$url_str = isset($item->id) ? "$controller/view/$item->id" : null ;
		$url = self::check_auth($controller, 'view_invisible') ? $url_str : '' ;
		$url = self::check_owner_auth($controller, 'view_invisible', $item) ? $url_str : '' ;

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
	private static function add_testdata($controller, $item)
	{
		$url = '';
		$usergroup_ids = \User\Controller_User::$userinfo['usergroup_ids'];

		//ルート管理者のみ
		if(in_array(-2, $usergroup_ids)):
			$url = "$controller/add_testdata";
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

	private static function index_all($controller, $item)
	{
		$url_str = isset($item->id) ? "$controller/index_all" : null ;
		$url = self::check_auth($controller, 'index_all') ? $url_str : '' ;

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

	private static function index_revision($controller, $item)
	{
		$url = 'oya/index_revision';
		/*
		$url_rev = $url ? "{$controller}/options_revisions/postcategories" : '';
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

	private static function revision_list($controller, $item)
	{
		$url = 'oya/revision_list';
		/*
		$url_rev = $url ? "{$controller}/options_revisions/postcategories" : '';
		$urls = array(
			array('カテゴリ設定', $url),
			array('カテゴリ設定履歴', $url_rev),
		);
		 */
		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
			'action_name'  => '記事ごとのリビジョン一覧',
			'menu_str'     => '記事ごとのリビジョン一覧',
			'explanation'  => '記事ごとのリビジョン一覧を閲覧する権限',
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

}
