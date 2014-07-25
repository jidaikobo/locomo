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
		$actions->index            = self::index($controller, $item);
		$actions->index_admin      = self::index_admin($controller, $item);
		$actions->view             = self::view($controller, $item);
		$actions->index_deleted    = self::index_deleted($controller, $item);
		$actions->view_deleted     = self::view_deleted($controller, $item);
		$actions->index_expired    = self::index_expired($controller, $item);
		$actions->view_expired     = self::view_expired($controller, $item);
		$actions->index_yet        = self::index_yet($controller, $item);
		$actions->view_yet         = self::view_yet($controller, $item);
		$actions->index_invisible  = self::index_invisible($controller, $item);
		$actions->view_invisible   = self::view_invisible($controller, $item);
		$actions->edit             = self::edit($controller, $item);
		$actions->create           = self::create($controller, $item);
		$actions->delete           = self::delete($controller, $item);
		$actions->undelete         = self::undelete($controller, $item);
		$actions->delete_deleted   = self::delete_deleted($controller, $item);
		$actions->view_revision    = self::view_revision($controller, $item);
		$actions->workflow         = self::workflow($controller, $item);
		$actions->workflow_actions = self::workflow_actions($controller, $item);
		$actions->add_testdata     = self::add_testdata($controller, $item);

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
	 * index_admin()
	 * @return  array
	 */
	private static function index_admin($controller, $item)
	{
		$url = "$controller/index_admin" ;
		$url = self::check_auth4url($controller, 'index_admin', $url);

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
	 * workflow()
	 * @return  array
	 */
	private static function workflow($controller, $item)
	{
		$retvals = array(
			'is_index'     => false,
			'url'          => '',
			'id_segment'   => null,
			'action_name'  => 'ワークフロー作業',
			'menu_str'     => 'ワークフロー作業',
			'explanation'  => 'ワークフロー管理下コントローラにおける新規作成、申請、編集権限です。承認設定は、ワークフローコントローラの経路設定で別途設定します。不可視項目の閲覧権限などに依存します。',
			'dependencies' => array(
				'view',
				'edit',
				'create',
				'index_admin',
				'index_invisible',
				'view_invisible',
				'apply',
				'route',
			)
		);
		return $retvals;
	}

	/**
	 * workflow_actions()
	 * 重たい処理。ワークフローが不要なコントローラでは読まないように注意。
	 * @return  array
	 */
	private static function workflow_actions($controller, $item)
	{
		$retval = array('dependencies'=>array());
		if(is_null($controller) || empty($item) || ! isset($item->id)) return $retval;

		//ステップを取得
		$model = \Workflow\Model_Workflow::forge();
		$current_step = $model::get_current_step($controller, $item->id);
		$route_id = $model::get_route($controller, $item->id);
		$total_step = $route_id ? $model::get_total_step($route_id) : -2;

		//-1の場合は、承認申請
		if($current_step == -1):
			$url = "{$controller}/apply/{$item->id}" ;
			$menu_str = '承認申請';
		elseif($current_step < $total_step):
		//ワークフロー進行中だったら承認・却下・差戻しができる
			$menus = array(
				array('承認',   "{$controller}/approve/{$item->id}"),
				array('却下',   "{$controller}/reject/{$item->id}"),
				array('差戻し', "{$controller}/remand/{$item->id}"),
			);
			$url = "" ;
			$menu_str = '';
		elseif($current_step == $total_step):
		//すでに承認が終わっていたら何もできない
			$url = "" ;
			$menu_str = '';
		endif;

		//経路が設定されていなければ、申請できない。経路設定URLを表示
		if(\Kontiki\Model_Workflow_Abstract::get_current_step($controller, $item->id) == '-1/N'):
			$url = "{$controller}/route/{$item->id}" ;
			$menu_str = '経路設定';
		endif;

		$retvals = array(
			'is_index'     => false,
			'url'          => $url,
			'id_segment'   => null,
			'action_name'  => 'ワークフロー作業（承認申請）',
			'menu_str'     => $menu_str,
			'explanation'  => 'ワークフロー管理下コントローラにおける承認申請です。「ワークフロー作業」を有効にすると自動的に有効になります。',
			'dependencies' => array()
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
			'confirm'      => true,
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
		$url = isset($item->id) ? "$controller/index_revision/$item->id" : null ;
		$url = self::check_auth4url($controller, 'index_revision', $url);

		$retvals = array(
			'url'          => $url,
			'id_segment'   => 3,
			'action_name'  => '閲覧（リビジョン）',
			'menu_str'     => '編集履歴',
			'explanation'  => '編集履歴の閲覧権限です。この権限を許可すると、元の項目が不可視、予約、期限切れ、削除済み等の状態であっても、履歴はみることができるようになります。また、通常項目の編集権限も許可されます。',
			'dependencies' => array(
				'view',
				'edit',
				'view_revision',
				'index_revision',
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
	
	/**
	 * download_files()
	 * @return  array
	 */
	private static function download_files($controller, $item)
	{
		$retvals = array(
			'action_name'  => 'ファイルへのアクセス権限',
			'explanation'  => 'アップロードされたファイルへのアクセス権限',
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
