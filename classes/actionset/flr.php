<?php
namespace Locomo;
class Actionset_Flr extends \Actionset_Base
{
	/**
	 * actionset_index_admin()
	 */
	public static function actionset_index_admin($controller, $obj = null, $id = null, $urls = array())
	{
		return array();
	}

	/**
	 * actionset_sync()
	 */
	public static function actionset_sync($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."sync", '同期'));
		$urls = static::generate_urls($controller.DS.'sync', $actions);

		$retvals = array(
			'realm'        => 'option',
			'urls'         => $urls,
			'action_name'  => '同期',
			'show_at_top'  => false,
			'explanation'  => 'ディレクトリとデータベースの内容を同期します。',
			'help'         => 'ディレクトリとデータベースの内容を同期します。ファイルやディレクトリの実際の状況とデータベースの内容に矛盾が生じているようでしたら、これを実行してください。この処理はファイルやディレクトリの量によっては時間がかかることがあります。',
			'acl_exp'      => 'ディレクトリとデータベースの内容を同期する権限です。',
			'order'        => 100,
			'dependencies' => array(
				$controller.DS.'sync',
			)
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
			$urls = static::generate_urls($controller.DS.'index_files', $actions);
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'order'        => 10,
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
			$actions = array(array($controller.DS."upload/".$obj->id, '新規アップロード'));
			$urls = static::generate_urls($controller.DS.'upload', $actions);
		}
		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'action_name'  => '新規作成',
			'show_at_top'  => false,
			'explanation'  => '新しい項目を追加します。',
			'help'         => '新しい項目を追加します。',
			'acl_exp'      => '新規アップロード権限。',
			'order'        => 20,
			'dependencies' => array(
				$controller.DS.'edit_file',
				$controller.DS.'view_file',
				$controller.DS.'upload',
			)
		);
		return $retvals;
	}

	/**
	 * actionset_create_dir()
	 */
	public static function actionset_create_dir($controller, $obj = null, $id = null, $urls = array())
	{
		if (@$obj->genre == 'dir')
		{
			$actions = array(array($controller.DS."create_dir/".$obj->id, '<!--ディレクトリ-->作成'));
			$urls = static::generate_urls($controller.DS.'upload', $actions);
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
			'dependencies' => array(
				$controller.DS.'edit',
				$controller.DS.'view',
				$controller.DS.'upload',
				$controller.DS.'index_admin',
				$controller.DS.'create_dir',
				$controller.DS.'rename_dir',
				$controller.DS.'move_dir',
				$controller.DS.'permission_dir',
			)
		);

		return $retvals;
	}

	/**
	 * actionset_move_dir()
	 */
	public static function actionset_move_dir($controller, $obj = null, $id = null, $urls = array())
	{
		if (@$obj->genre == 'dir')
		{
			$actions = array(array($controller.DS."move_dir/".$obj->id, '<!--ディレクトリ-->移動'));
			$urls = static::generate_urls($controller.DS.'move_dir', $actions);
		}
		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'order'        => 35,
		);
		return $retvals;
	}

	/**
	 * actionset_rename_dir()
	 */
	public static function actionset_rename_dir($controller, $obj = null, $id = null, $urls = array())
	{
		if (@$obj->genre == 'dir')
		{
			$actions = array(array($controller.DS."rename_dir/".$obj->id, '<!--ディレクトリ-->名称変更'));
			$urls = static::generate_urls($controller.DS.'rename_dir', $actions);
		}
		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'order'        => 40,
		);
		return $retvals;
	}

	/**
	 * actionset_permission_dir()
	 */
	public static function actionset_permission_dir($controller, $obj = null, $id = null, $urls = array())
	{
		if (@$obj->genre == 'dir')
		{
			$actions = array(array($controller.DS."permission_dir/".$obj->id, '<!--ディレクトリ-->権限設定'));
			$urls = static::generate_urls($controller.DS.'permission_dir', $actions);
		}
		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'order'        => 50,
		);
		return $retvals;
	}

	/**
	 * actionset_purge_dir()
	 */
	public static function actionset_purge_dir($controller, $obj = null, $id = null, $urls = array())
	{
		if (@$obj->genre == 'dir')
		{
			$actions = array(array($controller.DS."purge_dir/".$obj->id, '<!--ディレクトリ-->削除'));
			$urls = static::generate_urls($controller.DS.'purge_dir', $actions);
		}
		$retvals = array(
			'realm'        => 'base',
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
		if (\Request::active()->action == 'view_file' && $id)
		{
			$actions = array(array($controller.DS."edit_file".DS.$id, 'ファイル編集'));
			$urls = static::generate_urls($controller.DS.'upload', $actions);
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'action_name'  => 'ファイル編集',
			'show_at_top'  => false,
			'explanation'  => 'ファイル編集',
			'help'         => '既存ファイルの編集',
			'acl_exp'      => 'ファイル編集（名称変更等）権限。',
			'order'        => 50,
			'dependencies' => array(
				$controller.DS.'edit_file',
				$controller.DS.'view_file',
				$controller.DS.'upload',
			)
		);

		return $retvals;
	}

	/**
	 * actionset_view_file()
	 */
	public static function actionset_view_file($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Request::active()->action == 'edit_file' && $id)
		{
			$actions = array(array($controller.DS."view_file".DS.$id, 'ファイル詳細'));
			$urls = static::generate_urls($controller.DS.'upload', $actions);
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'action_name'  => 'ファイル詳細',
			'show_at_top'  => false,
			'explanation'  => 'ファイル詳細',
			'help'         => '既存ファイルの詳細情報',
			'acl_exp'      => 'ファイル詳細閲覧権限。',
			'order'        => 50,
			'dependencies' => array(
				$controller.DS.'edit_file',
				$controller.DS.'view_file',
				$controller.DS.'upload',
			)
		);

		return $retvals;
	}

	/**
	 * actionset_purge_file()
	 */
	public static function actionset_purge_file($controller, $obj = null, $id = null, $urls = array())
	{
		if (in_array(\Request::active()->action, ['view_file','edit_file']) && $id)
		{
			$actions = array(array($controller.DS."purge_file".DS.$id, 'ファイル削除'));
			$urls = static::generate_urls($controller.DS.'purge_file', $actions);
		}

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'action_name'  => 'ファイル削除',
			'show_at_top'  => false,
			'explanation'  => 'ファイル削除',
			'help'         => '既存ファイルの削除',
			'acl_exp'      => 'ファイル削除権限。',
			'order'        => 60,
			'dependencies' => array(
				$controller.DS.'purge_file',
				$controller.DS.'edit_file',
				$controller.DS.'view_file',
				$controller.DS.'upload',
			)
		);

		return $retvals;
	}
}
