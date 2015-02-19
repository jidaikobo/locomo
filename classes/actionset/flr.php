<?php
namespace Locomo;
class Actionset_Flr extends \Actionset
{
	/**
	 * actionset_admin()
	 */
	public static function actionset_admin($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = parent::actionset_admin($controller, $obj, $id);
		$actions = array(
			'\Controller_Flr::action_common_files',
			'\Controller_Flr::action_index_files',
			'\Controller_Flr::action_gallery',
			'\Controller_Flr_Dir::action_create',
			'\Controller_Flr_Dir::action_edit',
			'\Controller_Flr_Dir::action_permission',
			'\Controller_Flr_Dir::action_rename',
			'\Controller_Flr_File::action_edit',
			'\Controller_Flr_File::action_purge_file',
			'\Controller_Flr_File::action_upload',
			'\Controller_Flr_File::action_view',
			'\Controller_Flr_Sync::action_sync',
			'\Controller_Flr_Sync::action_dl',
		);
		\Arr::set($retvals, 'dependencies', $actions);
		\Arr::set($retvals, 'action_name', 'ファイラへのアクセス権');
		\Arr::set($retvals, 'acl_exp', 'ファイラの個別のアクセス権は、ファイルがアップロードされるディレクトリごとに設定します。ここで管理権限を設定しても、ディレクトリで設定された権限が優先します。');
		return $retvals;
	}

	/**
	 * dl()
	 */
	public static function actionset_dl($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'action_name'  => 'ダウンロード',
			'show_at_top'  => true,
			'explanation'  => 'ダウンロードします。',
			'help'         => '',
			'acl_exp'      => 'ダウンロード権限。',
			'order'        => 10,
			'dependencies' => array(
				'\Controller_Flr_File::action_dl',
			)
		);

		return $retvals;
	}

	/**
	 * actionset_tree()
	 */
	public static function actionset_tree($controller, $obj = null, $id = null, $urls = array())
	{
		$dirs = \Model_Flr::find('all', array('where' => array(array('genre', 'dir'))));
		if ( ! $dirs) return array();
		// now preparing
		return array();
	}

	/**
	 * actionset_root()
	 * provide link only
	 */
	public static function actionset_root($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array("\Controller_Flr/index_files", 'ルート'));
		$urls = static::generate_urls('\Controller_Flr::action_index_files', $actions);
		$retvals = array(
			'urls'  => $urls,
			'order' => 1,
		);
		return $retvals;
	}

	/**
	 * actionset_sync()
	 */
	public static function actionset_sync($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array("\Controller_Flr_Sync/sync", '同期'));
		$urls = static::generate_urls('\Controller_Flr_Sync::action_sync', $actions);

		$retvals = array(
			'realm'        => 'option',
			'urls'         => $urls,
			'action_name'  => '同期',
			'show_at_top'  => false,
			'explanation'  => 'ディレクトリとデータベースの内容を同期します。',
			'help'         => 'ディレクトリとデータベースの内容を同期します。ファイルやディレクトリの実際の状況とデータベースの内容に矛盾が生じているようでしたら、これを実行してください。この処理はファイルやディレクトリの量によっては時間がかかることがあります。',
			'acl_exp'      => 'ディレクトリとデータベースの内容を同期する権限です。',
			'order'        => 100,
		);
		return $retvals;
	}

	/**
	 * actionset_index_files()
	 */
	public static function actionset_index_files($controller, $obj = null, $id = null, $urls = array())
	{
		$parent = \Model_Flr::get_parent($obj);
		if ($parent && $obj->id != 1)
		{
			if ($obj->genre == 'dir')
			{
				$actions = array(array($controller.DS."index_files/".$parent->id, '上の階層'));
			} else {
				$actions = array(array($controller.DS."index_files/".$parent->id, 'ディレクトリへ'));
			}
			$urls = static::generate_urls($controller.'::action_index_files', $actions);
		}
		$retvals = array(
			'urls'         => $urls,
		);
		return $retvals;
	}

	/**
	 * actionset_upload()
	 */
	public static function actionset_upload($controller, $obj = null, $id = null, $urls = array())
	{
		if (@$obj->genre == 'dir')
		{
			$actions = array(array("\Controller_Flr_File/upload/".$obj->id, '新規アップロード'));
			$urls = static::generate_urls('\Controller_Flr_File::action_upload', $actions, ['create']);
		}
		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '新規作成',
			'show_at_top'  => false,
			'explanation'  => '新しい項目を追加します。',
			'help'         => '新しい項目を追加します。',
			'acl_exp'      => '新規アップロード権限。',
			'order'        => 20,
		);
		return $retvals;
	}

	/**
	 * actionset_create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		if (@$obj->genre == 'dir')
		{
			$actions = array(
				array("\Controller_Flr_Dir/create/".$obj->id, '<!--ディレクトリ-->作成'),
				array("\Controller_Flr_Dir/edit/".$obj->id, '編集')
			);
			$urls = static::generate_urls('\Controller_Flr_Dir::action_create', $actions, ['create']);
		}
		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'action_name'  => 'ディレクトリ作成、編集、削除',
			'show_at_top'  => true,
			'explanation'  => '新しいディレクトリを追加します。',
			'help'         => '新しいディレクトリを追加します。',
			'acl_exp'      => 'ディレクトリ作成、編集、削除権限。',
			'order'        => 30,
		);

		return $retvals;
	}

	/**
	 * actionset_rename()
	 */
	public static function actionset_rename($controller, $obj = null, $id = null, $urls = array())
	{
		if (@$obj->genre == 'dir')
		{
			$actions = array(array("\Controller_Flr_Dir/rename/".$obj->id, '<!--ディレクトリ-->名称変更'));
			$urls = static::generate_urls('\Controller_Flr_Dir::action_rename', $actions, ['create']);
		}
		$retvals = array(
			'urls'         => $urls,
			'order'        => 40,
		);
		return $retvals;
	}

	/**
	 * actionset_permission()
	 */
	public static function actionset_permission($controller, $obj = null, $id = null, $urls = array())
	{
		if (@$obj->genre == 'dir')
		{
			$actions = array(array("\Controller_Flr_Dir/permission/".$obj->id, '<!--ディレクトリ-->権限設定'));
			$urls = static::generate_urls('\Controller_Flr_Dir::action_permission', $actions, ['create']);
		}
		$retvals = array(
			'urls'         => $urls,
			'order'        => 50,
		);
		return $retvals;
	}

	/**
	 * actionset_purge()
	 */
	public static function actionset_purge($controller, $obj = null, $id = null, $urls = array())
	{
		if (@$obj->genre == 'dir')
		{
			$actions = array(array("\Controller_Flr_Dir/purge/".$obj->id, '<!--ディレクトリ-->削除'));
			$urls = static::generate_urls('\Controller_Flr_Dir::action_purge', $actions, ['create']);
		}
		$retvals = array(
			'urls'         => $urls,
			'order'        => 60,
		);
		return $retvals;
	}

	/**
	 * actionset_edit_file()
	 */
	public static function actionset_edit_file($controller, $obj = null, $id = null, $urls = array())
	{
		if ($controller == '\Controller_Flr_File' && \Request::active()->action == 'view' && $id)
		{
			$actions = array(array("\Controller_Flr_File/edit".DS.$id, 'ファイル編集'));
			$urls = static::generate_urls('\Controller_Flr_File::action_upload', $actions);
		}

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ファイル編集',
			'show_at_top'  => false,
			'explanation'  => 'ファイル編集',
			'help'         => '既存ファイルの編集',
			'acl_exp'      => 'ファイル編集（名称変更等）権限。',
			'order'        => 50,
		);

		return $retvals;
	}

	/**
	 * actionset_view_file()
	 */
	public static function actionset_view_file($controller, $obj = null, $id = null, $urls = array())
	{
		if ($controller == '\Controller_Flr_File' && \Request::active()->action == 'edit' && $id)
		{
			$actions = array(array("\Controller_Flr_File/view".DS.$id, 'ファイル詳細'));
			$urls = static::generate_urls('\Controller_Flr_File::action_upload', $actions);
		}

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ファイル詳細',
			'show_at_top'  => false,
			'explanation'  => 'ファイル詳細',
			'help'         => '既存ファイルの詳細情報',
			'acl_exp'      => 'ファイル詳細閲覧権限。',
			'order'        => 50,
		);

		return $retvals;
	}

	/**
	 * actionset_purge_file()
	 */
	public static function actionset_purge_file($controller, $obj = null, $id = null, $urls = array())
	{
		if ($controller == '\Controller_Flr_File')
		{
			if (in_array(\Request::active()->action, ['view','edit']) && $id)
			{
				$actions = array(array("\Controller_Flr_File/purge".DS.$id, 'ファイル削除'));
				$urls = static::generate_urls('\Controller_Flr_File::action_purge', $actions);
			}
		}

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ファイル削除',
			'show_at_top'  => false,
			'explanation'  => 'ファイル削除',
			'help'         => '既存ファイルの削除',
			'acl_exp'      => 'ファイル削除権限。',
			'order'        => 60,
		);

		return $retvals;
	}
}
