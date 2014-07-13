<?php
namespace Kontiki;
class Actionset
{
	/**
	 * actionItems()
	 * @return  obj
	 */
	public static function actionItems($controller = null, $item = null)
	{
		$actions = (object) array();
		$actions->index           = self::index($controller, $item);
		$actions->adminindex      = self::adminindex($controller, $item);
		$actions->view            = self::view($controller, $item);
		$actions->index_deleted   = self::index_deleted($controller, $item);
		$actions->view_deleted    = self::view_deleted($controller, $item);
		$actions->index_expired   = self::index_expired($controller, $item);
		$actions->view_expired    = self::view_expired($controller, $item);
		$actions->index_yet       = self::index_yet($controller, $item);
		$actions->view_yet        = self::view_yet($controller, $item);
		$actions->index_invisible = self::index_invisible($controller, $item);
		$actions->view_invisible  = self::view_invisible($controller, $item);
		$actions->edit            = self::edit($controller, $item);
		$actions->create          = self::create($controller, $item);
		$actions->delete          = self::delete($controller, $item);
		$actions->undelete        = self::undelete($controller, $item);
		$actions->delete_deleted  = self::delete_deleted($controller, $item);
		$actions->view_revision   = self::view_revision($controller, $item);
		$actions->add_testdata    = self::add_testdata($controller, $item);

//		$actions->download_files = self::download_files($controller, $item);
//		$actions->upload         = self::upload($controller, $item);
//		$actions->upload_all     = self::upload_all($controller, $item);
//		$actions->delete_file    = self::delete_file($controller, $item);
		return $actions;
	}

	/**
	 * check_auth4url()
	 * @return  array
	 */
	private static function check_auth4url($controller, $action, $url)
	{
		if(
			! \Acl\Controller_Acl::auth($controller.'/'.$action, \User\Controller_User::$userinfo) &&
			! \Acl\Controller_Acl::owner_auth($controller.'/'.$action, \User\Controller_User::$userinfo) 
		):
			$url = null ;
		endif;
		return $url;
	}

	/**
	 * index()
	 * @return  array
	 */
	private static function index($controller, $item)
	{
		$url = "$controller/index" ;
		$url = self::check_auth4url($controller, 'index', $url);

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
	 * adminindex()
	 * @return  array
	 */
	private static function adminindex($controller, $item)
	{
		$url = "$controller/adminindex" ;
		$url = self::check_auth4url($controller, 'adminindex', $url);

		$retvals = array(
			'is_index'     => true,
			'url'          => $url,
			'id_segment'   => null,
			'action_name'  => '管理者向け一覧（通常項目）',
			'menu_str'     => '管理者向け一覧',
			'explanation'  => '通常項目の一覧（管理者向け）の閲覧権限です。管理者向けですが閲覧できるのは通常項目のみです。削除済み項目等は個別に権限を付与してください。',
			'dependencies' => array(
				'adminindex',
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
		$url = isset($item->id) ? "$controller/view/$item->id" : null ;
		$url = self::check_auth4url($controller, 'view', $url);

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
	 * index_deleted()
	 * @return  array
	 */
	private static function index_deleted($controller, $item)
	{
		$url = "$controller/index_deleted" ;
		$url = self::check_auth4url($controller, 'index_deleted', $url);

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
		$url = isset($item->id) ? "$controller/view/$item->id" : null ;
		$url = self::check_auth4url($controller, 'view_deleted', $url);

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
		$url = "$controller/index_expired" ;
		$url = self::check_auth4url($controller, 'index_expired', $url);

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
		$url = isset($item->id) ? "$controller/view/$item->id" : null ;
		$url = self::check_auth4url($controller, 'index_expired', $url);

		$retvals = array(
			'is_index'     => true,
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
		$url = self::check_auth4url($controller, 'index_yet', $url);

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
		$url = isset($item->id) ? "$controller/view/$item->id" : null ;
		$url = self::check_auth4url($controller, 'view_yet', $url);

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
		$url = self::check_auth4url($controller, 'index_invisible', $url);

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
		$url = isset($item->id) ? "$controller/view/$item->id" : null ;
		$url = self::check_auth4url($controller, 'index_invisible', $url);

		$retvals = array(
			'is_index'     => true,
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
	 * edit()
	 * @return  array
	 */
	private static function edit($controller, $item)
	{
		$url = isset($item->id) ? "$controller/edit/$item->id" : null ;
		$url = self::check_auth4url($controller, 'edit', $url);

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
			'action_name'  => '項目の編集',
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
	 * create()
	 * @return  array
	 */
	private static function create($controller, $item)
	{
		$url = "$controller/create" ;
		$url = self::check_auth4url($controller, 'create', $url);

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
	 * edit_deleted()
	 * @return  array
	 */
	private static function edit_deleted($controller, $item)
	{
		$url = isset($item->id) ? "$controller/edit/$item->id" : null ;
		$url = self::check_auth4url($controller, 'edit_deleted', $url);

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
		$url = null ;
		if(isset($item->deleted_at) && $item->deleted_at == null):
			$url = isset($item->id) ? "$controller/delete/$item->id" : null ;
		endif;
		$url = self::check_auth4url($controller, 'delete', $url);

		//retval
		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
			'action_name'  => '項目の削除',
			'menu_str'     => '削除',
			'explanation'  => '項目を削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'dependencies' => array(
				'view',
				'view_deleted',
				'index_deleted',
				'delete',
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
		$url = null ;
		if(isset($item->deleted_at) && $item->deleted_at):
			$url = isset($item->id) ? "$controller/undelete/$item->id" : null ;
		endif;
		$url = self::check_auth4url($controller, 'undelete', $url);

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
		$url = null ;
		if(isset($item->deleted_at) && $item->deleted_at):
			$url = isset($item->id) ? "$controller/delete_deleted/$item->id" : null ;
		endif;
		$url = self::check_auth4url($controller, 'delete_deleted', $url);

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
	 * view_revision()
	 * @return  array
	 */
	private static function view_revision($controller, $item)
	{
		$url = isset($item->id) ? "$controller/revision/$item->id" : null ;
		$url = self::check_auth4url($controller, 'index_revision', $url);

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
			'action_name'  => '閲覧（リビジョン）',
			'menu_str'     => '更新履歴',
			'explanation'  => '作業履歴の閲覧権限です。この権限を許可すると、元の項目が不可視、予約、期限切れ、削除済み等の状態であっても、履歴はみることができるようになります。また、通常項目の編集権限も許可されます。',
			'dependencies' => array(
				'view',
				'view_revision',
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
		if(in_array(-2, $usergroup_ids) || in_array(-1, $usergroup_ids)):
			$url = "$controller/add_testdata";
		endif;

		$retvals = array(
			'admin_only'   => true,
			'url'          => $url,
			'id_segment'   => null,
			'action_name'  => 'テストデータの追加',
			'menu_str'     => 'テストデータの追加',
			'explanation'  => '開発者向けメニュー。テストデータの追加です。',
			'dependencies' => array(
				'add_testdata',
			)
		);
		return $retvals;
	}
	
	/**
	 * download_files()
	 * @return  array
	 */
	private static function download_files($controller, $item)
	{
		$retvals = array(
			'action_name' => 'ファイルへのアクセス権限',
			'explanation' => 'アップロードされたファイルへのアクセス権限',
			'dependencies' => array(
				'download',
			)
		);
		return $retvals;
	}
	
	/**
	 * upload()
	 * @return  array
	 */
	private static function upload($controller, $item)
	{
		$retvals = array(
			'action_name' => 'ファイルアップロード権限',
			'explanation' => '通常の項目に対するファイル添付の権限',
			'dependencies' => array(
				'upload',
			)
		);
		return $retvals;
	}
	
	/**
	 * upload_all()
	 * @return  array
	 */
	private static function upload_all($controller, $item)
	{
		$retvals = array(
			'action_name' => '強力なファイルアップロード権限',
			'explanation' => '不可視、予約、期限切れ、削除の項目などのファイルアップロード権限。また、セキュア領域へのファイルアップロードの権限でもあります。',
			'dependencies' => array(
				'upload_all',
			)
		);
		return $retvals;
	}
	
	/**
	 * delete_file()
	 * @return  array
	 */
	private static function delete_file($controller, $item)
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
