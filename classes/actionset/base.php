<?php
namespace Locomo;
class Actionset_Base extends Actionset
{
	/**
	 * create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action != 'create')
		{
			$urls = array(array($controller.DS."create", '新規作成'));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'action_name'  => '新規作成',
			'show_at_top'  => true,
			'explanation'  => '新しい項目を追加します。',
			'acl_exp'      => '新規作成権限。',
			'order'        => 10,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/create',
			)
		);

		return $retvals;
	}

	/**
	 * view()
	 */
	public static function actionset_view($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'edit' && $id)
		{
			$urls = array(array($controller.DS."view/".$id, '閲覧'));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls ,
			'action_name'  => '閲覧（通常項目）',
			'explanation'  => '通常項目の個票の閲覧権限です。',
			'acl_exp'      => '通常項目の個票の閲覧権限です。',
			'order'        => 20,
			'dependencies' => array(
				$controller.'/view',
			)
		);
		return $retvals;
	}

	/**
	 * edit()
	 */
	public static function actionset_edit($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'view' && $id)
		{
			$urls = array(array($controller.DS."edit/".$id, '編集'));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls ,
			'action_name'  => '編集（通常項目）',
			'explanation'  => '通常項目の編集権限。',
			'acl_exp'      => '通常項目の編集権限。',
			'order'        => 30,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/edit',
			)
		);
		return $retvals;
	}

	/**
	 * edit_deleted()
	 */
	public static function actionset_edit_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'view' && $id)
		{
			$urls = array(array($controller.DS."edit/".$id, '編集'));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls ,
			'action_name'  => '削除された項目の編集',
			'explanation'  => '削除された項目の編集権限です。削除された項目の閲覧権限も付与されます。',
			'acl_exp'      => '削除された項目の編集権限です。削除された項目の閲覧権限も付与されます。',
			'order'        => 30,
			'dependencies' => array(
				$controller.'/index_deleted',
				$controller.'/view_deleted',
				$controller.'/edit_deleted',
			)
		);
		return $retvals;
	}
	
	/**
	 * delete()
	 */
	public static function actionset_delete($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action != 'create' && isset($obj->deleted_at) && is_null($obj->deleted_at) && $id)
		{
			$urls = array(array($controller.DS."delete/".$id, '削除', array('class' => 'confirm', 'data-jslcm-msg' => '削除してよいですか？')));
		}

		//retval
		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls ,
			'action_name'  => '項目の削除',
			'explanation'  => '項目を削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'acl_exp'      => '項目を削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 40,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/view_deleted',
				$controller.'/index_deleted',
				$controller.'/delete',
				$controller.'/confirm_delete',
			)
		);
		return $retvals;
	}

	/**
	 * undelete()
	 */
	public static function actionset_undelete($controller, $obj = null, $id = null, $urls = array())
	{
		if (isset($obj->deleted_at) && $obj->deleted_at && $id)
		{
			$urls = array(array($controller.DS."undelete/".$id, '復活', array('class' => 'confirm', 'data-jslcm-msg' => '項目を復活してよいですか？')));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls ,
			'action_name'  => '項目の復活',
			'explanation'  => '削除された項目を復活する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'acl_exp'      => '削除された項目を復活する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 50,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/view_deleted',
				$controller.'/index_deleted',
				$controller.'/undelete',
			)
		);
		return $retvals;
	}

	/**
	 * delete_deleted()
	 */
	public static function actionset_delete_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		if (isset($obj->deleted_at) && $obj->deleted_at && $id)
		{
			$urls = array(array($controller.DS."delete_deleted/".$id, '完全削除', array('class' => 'confirm', 'data-jslcm-msg' => '完全に削除してよいですか？')));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls ,
			'action_name'  => '項目の完全な削除',
			'explanation'  => '削除された項目を復活できないように削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'acl_exp'      => '削除された項目を復活できないように削除する権限です。通常項目の閲覧権限と、削除された項目の閲覧権限も付与されます。',
			'order'        => 50,
			'dependencies' => array(
				$controller.'/view',
				$controller.'/view_deleted',
				$controller.'/index_deleted',
				$controller.'/delete_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_deleted()
	 */
	public static function actionset_view_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action != 'view' && isset($obj->deleted_at) && $obj->deleted_at && $id)
		{
			$urls = array(array($controller.DS."view/".$id, '閲覧'));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls ,
			'action_name'  => '閲覧（削除された項目）',
			'explanation'  => '削除された項目の閲覧権限です。削除権限、復活権限は別に設定する必要があります。',
			'acl_exp'      => '削除された項目の閲覧権限です。削除権限、復活権限は別に設定する必要があります。',
			'order'        => 20,
			'dependencies' => array(
				$controller.'/view_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * view_expired()
	 */
	public static function actionset_view_expired($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'edit' && $id)
		{
			$urls = array(array($controller.DS."view/".$id, '閲覧'));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls ,
			'action_name'  => '閲覧（期限切れ）',
			'explanation'  => '期限切れ項目の閲覧権限です。',
			'acl_exp'      => '期限切れ項目の閲覧権限です。',
			'order'        => 20,
			'dependencies' => array(
				$controller.'/view_expired',
			)
		);
		return $retvals;
	}

	/**
	 * view_yet()
	 */
	public static function actionset_view_yet($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'edit' && $id)
		{
			$urls = array(array($controller.DS."view/".$id, '閲覧'));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls ,
			'action_name'  => '閲覧（予約項目）',
			'explanation'  => '予約項目の閲覧権限です。',
			'acl_exp'      => '予約項目の閲覧権限です。',
			'order'        => 20,
			'dependencies' => array(
				$controller.'/index_yet',
				$controller.'/view_yet',
			)
		);
		return $retvals;
	}

	/**
	 * view_invisible()
	 */
	public static function actionset_view_invisible($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::main()->action == 'edit' && $id)
		{
			$urls = array(array($controller.DS."view/".$id, '閲覧'));
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls ,
			'action_name'  => '閲覧（不可視項目）',
			'explanation'  => '不可視項目の閲覧権限',
			'acl_exp'      => '不可視項目の閲覧権限',
			'order'        => 20,
			'dependencies' => array(
				$controller.'/index_invisible',
				$controller.'/view_invisible',
			)
		);
		return $retvals;
	}

	/**
	 * index()
	 */
	public static function actionset_index($controller, $obj = null, $id = null, $urls = array())
	{
		$urls = array(array($controller.DS."index", '公開一覧'));

		$retvals = array(
			'realm'        => 'index',
			'urls'         => $urls ,
			'action_name'  => '一覧（通常項目）',
			'explanation'  => '通常項目の一覧の閲覧権限です。',
			'acl_exp'      => '通常項目の一覧の閲覧権限です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.'/index',
			)
		);
		return $retvals;
	}

	/**
	 * index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		// count
		$model = str_replace('Controller', 'Model', $controller);

		if ( ! class_exists($model)) return array();

		// urls
		$count = $model::count();
		$urls = array(array($controller.DS."index_admin", "管理一覧 ({$count})"));

		$retvals = array(
			'realm'        => 'index',
			'urls'         => $urls ,
			'action_name'  => '管理者向け一覧（通常項目）',
			'show_at_top'  => false,
			'explanation'  => '通常項目の一覧（管理者向け）の閲覧権限です。管理者向けですが閲覧できるのは通常項目のみです。削除済み項目等は個別に権限を付与してください。',
			'acl_exp'      => '通常項目の一覧（管理者向け）の閲覧権限です。管理者向けですが閲覧できるのは通常項目のみです。削除済み項目等は個別に権限を付与してください。',
			'order'        => 10,
			'dependencies' => array(
				$controller.'/index_admin',
			)
		);
		return $retvals;
	}

	/**
	 * index_deleted()
	 */
	public static function actionset_index_deleted($controller, $obj = null, $id = null, $urls = array())
	{
		// count
		static $count;
		$model = str_replace('Controller', 'Model', $controller);
		if (class_exists($model) && isset($model::properties()['deleted_at']))
		{
			$model::disable_filter();
			$count = $model::count(array('where' => array(array('deleted_at', 'is not' , NULL))));
			// $model::enable_filter();
		}

		// urls
		$urls = array(array($controller.DS."index_deleted", "ごみ箱 ({$count})"));

		$retvals = array(
			'realm'        => 'index',
			'urls'         => $urls ,
			'action_name'  => '一覧（削除された項目）',
			'explanation'  => '削除された項目一覧です。',
			'acl_exp'      => '削除された項目一覧の権限です。',
			'order'        => 20,
			'dependencies' => array(
				$controller.'/index_deleted',
			)
		);
		return $retvals;
	}

	/**
	 * index_yet()
	 */
	public static function actionset_index_yet($controller, $obj = null, $id = null, $urls = array())
	{
		// count
		static $count;
		$model = str_replace('Controller', 'Model', $controller);
		if (class_exists($model) && ! $count && isset($model::properties()['created_at']))
		{
			$count = $model::count(array('where' => array(array('created_at', '>' , date('Y-m-d H:i:s')))));
		}

		// urls
		$count = " ({$count})";
		$urls = array(array($controller.DS."index_yet", "予約項目{$count}"));

		$retvals = array(
			'realm'        => 'index',
			'urls'         => $urls ,
			'action_name'  => '一覧（予約項目）',
			'explanation'  => '予約項目一覧です。',
			'acl_exp'      => '予約項目一覧の権限です。',
			'order'        => 30,
			'dependencies' => array(
				$controller.'/index_yet',
			)
		);
		return $retvals;
	}

	/**
	 * index_expired()
	 */
	public static function actionset_index_expired($controller, $obj = null, $id = null, $urls = array())
	{
		// count
		static $count;
		$model = str_replace('Controller', 'Model', $controller);
		if (class_exists($model) && ! $count && isset($model::properties()['expired_at']))
		{
			$count = $model::count(array('where' => array(array('expired_at', '<' , date('Y-m-d H:i:s')))));
		}

		// urls
		$count = " ({$count})";
		$urls = array(array($controller.DS."index_expired", "期限切れ項目{$count}"));

		$retvals = array(
			'realm'        => 'index',
			'urls'         => $urls ,
			'action_name'  => '一覧（期限切れ項目）',
			'explanation'  => '期限切れ項目一覧です。',
			'acl_exp'      => '期限切れ項目一覧の権限です。',
			'order'        => 40,
			'dependencies' => array(
				$controller.'/index_expired',
			)
		);
		return $retvals;
	}

	/**
	 * index_invisible()
	 */
	public static function actionset_index_invisible($controller, $obj = null, $id = null, $urls = array())
	{
		// count
		static $count;
		$model = str_replace('Controller', 'Model', $controller);
		if (class_exists($model) && ! $count && isset($model::properties()['is_visible']))
		{
			$count = $model::count(array('where' => array(array('is_visible', '=' , false))));
		}

		// urls
		$count = " ({$count})";
		$urls = array(array($controller.DS."index_invisible", "不可視項目{$count}"));

		$retvals = array(
			'realm'        => 'index',
			'urls'         => $urls ,
			'action_name'  => '一覧（不可視項目）',
			'explanation'  => '不可視項目一覧です。',
			'acl_exp'      => '不可視項目一覧の権限です。',
			'order'        => 50,
			'dependencies' => array(
				$controller.'/index_invisible',
			)
		);
		return $retvals;
	}

	/**
	 * index_all()
	 */
	public static function actionset_index_all($controller, $obj = null, $id = null, $urls = array())
	{
		// count
		static $count;
		$model = str_replace('Controller', 'Model', $controller);
		if (class_exists($model) && ! $count)
		{
			$pk = $model::get_primary_keys('first');
			$model::disable_filter();
			$count = $model::count(array('where' => array(array($pk, 'is not' , null))));
		}

		// urls
		$count = " ({$count})";
		$urls = array(array($controller.DS."index_all", "すべて{$count}"));

		$retvals = array(
			'realm'        => 'index',
			'urls'         => $urls ,
			'action_name'  => '削除を含む全項目一覧',
			'explanation'  => '全項目項目一覧です。',
			'acl_exp'      => '全項目項目一覧の権限です。この権限を許可するとすべてのインデクスへのアクセス権を付与されます。',
			'order'        => 100,
			'dependencies' => array(
				$controller.'/index_admin',
				$controller.'/index_deleted',
				$controller.'/index_expired',
				$controller.'/index_yet',
				$controller.'/index_invisible',
				$controller.'/index_all',
			)
		);
		return $retvals;
	}
}
