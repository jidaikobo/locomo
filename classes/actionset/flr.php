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
			'\Controller_Flr/common_files',
			'\Controller_Flr/index_files',
			'\Controller_Flr/gallery',
			'\Controller_Flr_Dir/create',
			'\Controller_Flr_Dir/edit',
			'\Controller_Flr_Dir/permission',
			'\Controller_Flr_Dir/rename',
			'\Controller_Flr_File/edit',
			'\Controller_Flr_File/purge_file',
			'\Controller_Flr_File/upload',
			'\Controller_Flr_File/view',
			'\Controller_Flr_File/dl',
			'\Controller_Flr_Sync/sync',
		);
		\Arr::set($retvals, 'dependencies', $actions);
		\Arr::set($retvals, 'action_name', 'ファイラへのアクセス権');
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
			'order'        => 10,
			'dependencies' => array(
				'\Controller_Flr_File/dl',
			)
		);

		return $retvals;
	}

	/**
	 * actionset_root()
	 * provide link only
	 */
	public static function actionset_root($controller, $obj = null, $id = null, $urls = array())
	{
		$urls = array(array("\Controller_Flr/index_files", 'ルート'));
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
		$urls = array(array("\Controller_Flr_Sync/sync", '同期'));
		$retvals = array(
			'realm'        => 'option',
			'urls'         => $urls,
			'action_name'  => '同期',
			'show_at_top'  => false,
			'explanation'  => 'ディレクトリとデータベースの内容を同期します。',
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
				$urls = array(array($controller.DS."index_files/".$parent->id, '上の階層'));
			} else {
				$urls = array(array($controller.DS."index_files/".$parent->id, 'ディレクトリへ'));
			}
		}
		$retvals = array(
			'urls' => $urls,
		);
		return $retvals;
	}

	/**
	 * actionset_upload()
	 */
	public static function actionset_upload($controller, $obj = null, $id = null, $urls = array())
	{
		// ファイラ固有のアクセス権を確認する
		if ( ! isset($obj->path) || ! \Controller_Flr::check_auth($obj->path, 'upload')) return array();

		if (@$obj->genre == 'dir')
		{
			$urls = array(array("\Controller_Flr_File/upload/".$obj->id, '新規アップロード'));
		}
		$retvals = array(
			'urls'         => $urls,
			'action_name'  => '新規作成',
			'show_at_top'  => false,
			'explanation'  => '新しい項目を追加します。',
			'order'        => 20,
		);
		return $retvals;
	}

	/**
	 * actionset_create()
	 */
	public static function actionset_create($controller, $obj = null, $id = null, $urls = array())
	{
		// ファイラ固有のアクセス権を確認する
		if ( ! isset($obj->path) || ! \Controller_Flr::check_auth($obj->path, 'create_dir')) return array();

		if (@$obj->genre == 'dir')
		{
			$urls = array(
				array("\Controller_Flr_Dir/create/".$obj->id, '<!--ディレクトリ-->作成'),
			);
			$urls[] = array("\Controller_Flr_Dir/edit/".$obj->id, '編集');
		}
		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'action_name'  => 'ディレクトリ作成、編集',
			'show_at_top'  => true,
			'explanation'  => '新しいディレクトリを追加します。',
			'order'        => 30,
		);

		return $retvals;
	}

	/**
	 * actionset_purge()
	 */
	public static function actionset_purge($controller, $obj = null, $id = null, $urls = array())
	{
		// ファイラ固有のアクセス権を確認する
		if ( ! isset($obj->path) || ! \Controller_Flr::check_auth($obj->path, 'purge_dir')) return array();

		// ルートディレクトリは削除の対象外
		if ($obj->path == '/') return array();

		if (\Request::main()->action != 'create' && @$obj->genre == 'dir')
		{
			$urls = array(array("\Controller_Flr_Dir/purge/".$obj->id, '<!--ディレクトリ-->削除'));
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
		// ファイラ固有のアクセス権を確認する - アップロードできる人は編集できる
		if ( ! isset($obj->path) || ! \Controller_Flr::check_auth($obj->path, 'upload')) return array();

		if ($controller == '\Controller_Flr_File' && \Request::active()->action == 'view' && $id)
		{
			$urls = array(array("\Controller_Flr_File/edit".DS.$id, 'ファイル編集'));
		}

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ファイル編集',
			'show_at_top'  => false,
			'explanation'  => 'ファイル編集',
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
			$urls = array(array("\Controller_Flr_File/view".DS.$id, 'ファイル詳細'));
		}

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ファイル詳細',
			'show_at_top'  => false,
			'explanation'  => 'ファイル詳細',
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
				$urls = array(array("\Controller_Flr_File/purge".DS.$id, 'ファイル削除'));
			}
		}

		$retvals = array(
			'urls'         => $urls,
			'action_name'  => 'ファイル削除',
			'show_at_top'  => false,
			'explanation'  => 'ファイル削除',
			'order'        => 60,
		);

		return $retvals;
	}
}
