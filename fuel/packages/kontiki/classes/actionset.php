<?php
namespace Kontiki;
class Actionset
{
	/**
	 * actionItems()
	 * @param str $controller
	 * @return  obj
	 */
	public static function actionItems($controller = null)
	{
		$actions = (object) array();
		$actions->view           = self::view($controller);
		$actions->view_revision  = self::view_revision();
		$actions->view_expired   = self::view_expired();
		$actions->view_yet       = self::view_yet();
		$actions->view_deleted   = self::view_deleted();
		$actions->view_invisible = self::view_invisible();
		$actions->download_files = self::download_files();
		$actions->create         = self::create();
		$actions->edit           = self::edit();
		$actions->upload         = self::upload();
		$actions->upload_all     = self::upload_all();
		$actions->delete_files   = self::delete_files();
		$actions->delete         = self::delete();
		$actions->undelete       = self::undelete();
		$actions->purge          = self::purge();
		return $actions;
	}

	/**
	 * judge_set()
	 *
	 * @param str   $actions
	 * @param array $actionsets
	 *
	 * @return  array
	 */
	public static function judge_set($actions, $actionsets)
	{
		//アクションセットの条件を満たすものを抽出
		$results = array();
		foreach($actionsets as $actionset_name => $v){
			if( ! array_diff($v['dependencies'], $actions)){
				$results[] = $actionset_name;
			};
		}
		return $results;
	}

	/**
	 * view()
	 * @param str   $controller
	 * @return  array
	 */
	private static function view($controller)
	{
		$retvals = array(
			'owner_allowed' => TRUE,
			'url' => '/'.$controller.'/index/',
			'action_name' => '閲覧（通常項目）',
			'explanation' => '通常項目の一覧と個票の閲覧権限です。',
			'dependencies' => array(
				'index',
				'view',
			)
		);
		return $retvals;
	}

	/**
	 * view_revision()
	 * @return  array
	 */
	private static function view_revision()
	{
		$retvals = array(
			'owner_allowed' => TRUE,
			'action_name' => '閲覧（リビジョン）',
			'explanation' => '作業履歴の閲覧権限です。この権限を許可すると、元の項目が不可視、予約、期限切れ、削除済み等の状態であっても、履歴はみることができるようになります。また、通常項目の編集権限も許可されます。',
			'dependencies' => array(
				'index',
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
	private static function view_expired()
	{
		$retvals = array(
			'owner_allowed' => TRUE,
			'action_name' => '閲覧（期限切れ）',
			'explanation' => '期限切れ項目の閲覧権限です。',
			'dependencies' => array(
				'index_expired',
				'view_expired',
			)
		);
		return $retvals;
	}
	
	/**
	 * view_yet()
	 * @return  array
	 */
	private static function view_yet()
	{
		$retvals = array(
			'owner_allowed' => TRUE,
			'action_name' => '閲覧（予約項目）',
			'explanation' => '予約項目の閲覧権限です。',
			'dependencies' => array(
				'index_yet',
				'view_yet',
			)
		);
		return $retvals;
	}
	
	/**
	 * view_deleted()
	 * @return  array
	 */
	private static function view_deleted()
	{
		$retvals = array(
			'owner_allowed' => TRUE,
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
	private static function view_invisible()
	{
		$retvals = array(
			'owner_allowed' => TRUE,
			'action_name' => '閲覧（不可視項目）',
			'explanation' => '不可視項目の閲覧権限',
			'dependencies' => array(
				'index_invisible',
				'view_invisible',
			)
		);
		return $retvals;
	}
	
	/**
	 * create()
	 * @return  array
	 */
	private static function create()
	{
		$retvals = array(
			'action_name' => '新規作成',
//			'explanation' => '新規作成権限。コントローラがワークフロー管理されているときには、非表示コンテンツしか作成できません。',
			'explanation' => '新規作成権限',
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
	private static function edit()
	{
		$retvals = array(
			'owner_allowed' => TRUE,
			'action_name' => '項目の編集',
//			'explanation' => '特殊な条件のない項目の編集権限。この権限しか付与されていないユーザは、項目を不可視、予約、期限切れにしたり、項目をごみ箱に入れることもできません。またコントローラがワークフロー管理されているときには、表示・非表示設定も変更できません。',
			'explanation' => '特殊な条件のない項目の編集権限',
			'dependencies' => array(
				'index',
				'view',
				'edit',
			)
		);

		return $retvals;
	}

	/**
	 * edit_deleted()
	 * @return  array
	 */
	private static function edit_deleted()
	{
		$retvals = array(
			'action_name' => '削除された項目の編集',
			'explanation' => '削除された項目の編集権限です。削除された項目の閲覧権限も付与されます。',
			'dependencies' => array(
				'index_deleted',
				'view_deleted',
				'edit_deleted',
			)
		);
		return $retvals;
	}
	
	/**
	 * download_files()
	 * @return  array
	 */
	private static function download_files()
	{
		$retvals = array(
			'action_name' => 'ファイルへのアクセス権限',
			'explanation' => 'セキュア領域に置かれたファイルへのアクセス権限',
			'dependencies' => array(
			)
		);
		return $retvals;
	}
	
	/**
	 * upload()
	 * @return  array
	 */
	private static function upload()
	{
		$retvals = array(
			'action_name' => 'ファイルアップロード権限',
			'explanation' => '通常の項目に対するファイル添付の権限',
			'dependencies' => array(
			)
		);
		return $retvals;
	}
	
	/**
	 * upload_all()
	 * @return  array
	 */
	private static function upload_all()
	{
		$retvals = array(
			'action_name' => '強力なファイルアップロード権限',
			'explanation' => '不可視、予約、期限切れ、ごみ箱の項目などのファイルアップロード権限。また、セキュア領域へのファイルアップロードの権限でもあります。',
			'dependencies' => array(
			)
		);
		return $retvals;
	}
	
	/**
	 * delete_files()
	 * @return  array
	 */
	private static function delete_files()
	{
		$retvals = array(
			'action_name' => 'ファイルの削除権限',
			'explanation' => '添付ファイルの削除権限です。',
			'dependencies' => array(
			)
		);
		return $retvals;
	}
	
	/**
	 * delete()
	 * @return  array
	 */
	private static function delete()
	{
		$retvals = array(
			'action_name' => '項目の削除',
			'explanation' => '項目を削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'dependencies' => array(
				'index',
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
	private static function undelete()
	{
		$retvals = array(
			'action_name' => '項目の復活',
			'explanation' => '削除された項目を復活する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
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
	 * purge()
	 * @return  array
	 */
	private static function purge()
	{
		$retvals = array(
			'no_acl' => TRUE,
			'action_name' => '項目の完全な削除',
			'explanation' => '削除された項目を復活できないように削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'dependencies' => array(
				'index',
				'view',
				'view_deleted',
				'index_deleted',
				'purge',
			)
		);
		return $retvals;
	}
}
