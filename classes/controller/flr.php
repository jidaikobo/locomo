<?php
namespace Locomo;
class Controller_Flr extends \Locomo\Controller_Base
{
	// locomo
	public static $locomo = array(
		'nicename'     => 'ファイル', // for human's name
		'explanation'  => 'ファイルの閲覧やアップロードを行います。', // for human's explanation
		'main_action'  => 'index_files', // main action
		'main_action_name' => 'ファイル管理', // main action's name
		'main_action_explanation' => 'ファイルのアップロードや、アップロードされたファイルの閲覧を行います。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => true, // true: hide from admin bar
		'order'        => 1030, // order of appearance
		'widgets' =>array(
		),
	);

	/**
	 * before()
	 */
	public function before()
	{
		parent::before();
		if (\Str::ends_with(LOCOMOUPLOADPATH, DS)) throw new \Exception("LOCOMOUPLOADPATH must not be terminated with '/'");
	}

	/**
	 * sync()
	 */
	protected static function sync()
	{
		$items = \Util::get_file_list(LOCOMOUPLOADPATH);
		$basepath_len = strlen(LOCOMOUPLOADPATH);

		// tmp - clone flr table
		if (\DBUtil::table_exists('lcm_flrs_tmp')) \DBUtil::drop_table('lcm_flrs_tmp');
		\DB::query('CREATE TABLE lcm_flrs_tmp like lcm_flrs;')->execute();
		\DB::query('INSERT INTO lcm_flrs_tmp SELECT * FROM lcm_flrs;')->execute();
		\DBUtil::truncate_table('lcm_flrs');

		// tmp - clone flr_permissions table
		if (\DBUtil::table_exists('lcm_flr_permissions_tmp')) \DBUtil::drop_table('lcm_flr_permissions_tmp');
		\DB::query('CREATE TABLE lcm_flr_permissions_tmp like lcm_flr_permissions;')->execute();
		\DB::query('INSERT INTO lcm_flr_permissions_tmp SELECT * FROM lcm_flr_permissions;')->execute();
		\DBUtil::truncate_table('lcm_flr_permissions');

		// save
		foreach ($items as $fullpath)
		{
			$obj = \Model_Flr::forge();
			$num = is_dir($fullpath) ? 2 : 1;
			$depth = count(explode('/', substr($fullpath, $basepath_len))) - $num;
			$path = substr($fullpath, $basepath_len);
			$current = \Model_Flr::fetch_hidden_info($fullpath);// .LOCOMO_DIR_INFO

			// set obj
			$basename = basename($fullpath);
			$obj->name        = $basename;
			$obj->ext         = is_dir($fullpath) ? '' : substr($basename, strrpos($basename, '.') + 1) ;
			$obj->genre       = is_dir($fullpath) ? 'dir' : \Locomo\File::get_file_genre($basename);
			$obj->explanation = \Arr::get($current, md5($path).'.data.explanation', '');
			$obj->is_visible  = \Arr::get($current, md5($path).'.data.is_visible', 1);
			$obj->is_sticky   = \Arr::get($current, md5($path).'.data.is_sticky', 0);
			$obj->depth       = $depth;
			$obj->path        = $path;
			$obj->created_at  = date('Y-m-d H:i:s', \File::get_time($fullpath, 'created'));
			$obj->updated_at  = date('Y-m-d H:i:s', \File::get_time($fullpath, 'modified'));

			// relations
			$usergroups = \Arr::get($current, md5($path).'.permission_usergroup', array());
			foreach ($usergroups as $id => $usergroup)
			{
				$obj->permission_usergroup[$id] = \Model_Flr_Usergroup::forge()->set($usergroup);
			}
			$users = \Arr::get($current, md5($path).'.permission_user', array());
			foreach ($users as $id => $user)
			{
				$obj->permission_user[$id] = \Model_Flr_User::forge()->set($user);
			}

			$obj->save();
		}

		// update .LOCOMO_DIR_INFO - overhead but until here there is no data at lcm_flrs.
		foreach ($items as $fullpath)
		{
			$path = substr($fullpath, $basepath_len);
			$tmp_obj = Model_Flr::find('first', array('where' => array(array('path', $path))));
			Model_Flr::embed_hidden_info($tmp_obj);
		}

		// check
		if ($obj->count() == count($items))
		{
			\DBUtil::drop_table('lcm_flrs_tmp');
			\DBUtil::drop_table('lcm_flr_permissions_tmp');
			return true;
		}
		else
		{
			\DBUtil::drop_table('lcm_flrs');
			\DBUtil::drop_table('lcm_flr_permissions');
			\DBUtil::rename_table('lcm_flrs_tmp', 'lcm_flrs');
			\DBUtil::rename_table('lcm_flr_permissions_tmp', 'lcm_flr_permissions');
			return false;
		}
	}

	/**
	 * action_index_files()
	 */
	public function action_index_files($id = 1)
	{
		// current dir
		$current_obj = \Model_Flr::find($id, \Model_Flr::authorized_option(array(), 'index_files'));

		if ($current_obj)
		{
			// children
			$objs = \Model_Flr::get_children($current_obj);
		}
		else
		{
			static::sync();
			\Response::redirect(\Uri::create('flr/index_files/'));
		}

		// view
		$this->set_object($current_obj);
		$content = \View::forge('flr/index_files');
		$content->set_global('current', $current_obj);
		$content->set('items', $objs);
		$this->template->content = $content;
		$this->template->set_global('title', 'ファイル一覧');
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
/*
		if (\Input::get('from')) \Model_Flr::$_conditions['where'][] = array('created_at', '>=', \Input::get('from'));
		if (\Input::get('to'))   \Model_Flr::$_conditions['where'][] = array('created_at', '<=', \Input::get('to'));
		\Model_Flr::$_conditions['order_by'][] = array('name', 'ASC');
		parent::index_admin();
*/
		

	}

	/**
	 * action_sync()
	 * 現状のディレクトリと同期。データベースは現状のディレクトリにあわせて刷新される
	 */
	public function action_sync($id = null)
	{
		// sync
		if (\Input::post())
		{
			if (static::sync())
			{
				\Session::set_flash('success', "データベースとディレクトリを同期しました。");
			} else {
				\Session::set_flash('error', "データベースとディレクトリの同期に失敗しました。");
			}
		}

		// view
		$form = \Model_Flr::sync_definition();
		$content = \View::forge('flr/edit');
		$content->set_safe('form', $form);
		$this->template->content = $content;
		$this->template->set_global('title', '同期');
	}

	/**
	 * action_create_dir()
	 * ディレクトリの作成
	 */
	public function action_create_dir($id = null)
	{
		// create dir
		if (\Input::post())
		{
			$parent =  \Input::post('parent');
			$dirnname = \Input::post('name');
			$path = $parent.$dirnname;
			$tmp_obj = Model_Flr::find('first', array('where' => array(array('path', $path))));

			if ($tmp_obj && file_exists(LOCOMOUPLOADPATH.$parent.$dirnname))
			{
				\Session::set_flash('error', 'そのディレクトリは既に存在します。');
				\Response::redirect(\Uri::create('flr/create_dir/'.$id));
			}
			elseif ( ! $tmp_obj && file_exists(LOCOMOUPLOADPATH.$parent.$dirnname))
			{
				\Session::set_flash('error', '物理ディレクトリは存在していますが、データベース上にディレクトリが存在しなかったので、物理ディレクトリを作成せず、データベースのみをアップデートしました。');
			}
			elseif ( ! \File::create_dir(LOCOMOUPLOADPATH.$parent, $dirnname))
			{
				\Session::set_flash('error', 'ディレクトリの新規作成に失敗しました。');
				\Response::redirect(\Uri::create('flr/create_dir/'.$id));
			}
		}

		// parent::edit()
		$obj = parent::edit();

		// rewrite message
		$success = \Session::get_flash('success');
		if ($success && $obj)
		{
			static::sync();
			\Session::set_flash('success', "ディレクトリを新規作成しました。");
			static::$redirect = 'flr/permission_dir/'.$obj->id;
		}

		// assign
		$this->template->set_global('title', 'ディレクトリ作成');
	}

	/**
	 * action_rename_dir()
	 * ディレクトリのリネーム
	 */
	public function action_rename_dir($id = null)
	{
		$model = $this->model_name;
		$errors = array();

		// rename dir
		if (\Input::post())
		{
/*
			$obj = \Model_Flr::find($id, \Model_Flr::authorized_option(array(), 'edit'));
			$prev_name = $obj->name;
			$new_name = \Input::post('name');
			$parent = dirname($obj->path).DS;

			// rename
			if ($prev_name != $new_name)
			{
				if( ! \File::rename_dir($parent.$prev_name, $parent.$new_name))
				{
					$errors[] = 'ディレクトリのリネームに失敗しました。';
				}
			}
*/
		}

		// parent::edit()
		$obj = parent::edit($id);

		// error
		if($errors) \Session::set_flash('error', $errors);

		// rewrite message
		$success = \Session::get_flash('success');
		if ($success)
		{
			static::sync();
			\Session::set_flash('success', "ディレクトリをリネームしました。");
		}

		// assign
		$this->template->set_global('title', 'ディレクトリリネーム');
	}

	/**
	 * action_move_dir()
	 * ディレクトリの移動
	 */
	public function action_move_dir($id = null)
	{
		$model = $this->model_name;
		$errors = array();

		// rename dir
		if (\Input::post())
		{
			$obj = \Model_Flr::find($id, \Model_Flr::authorized_option(array(), 'edit'));
			$parent = \Input::post('parent');
			$dirnname = \Input::post('name');

			// move
			if ($obj->path != $parent.$dirnname)
			{
				$flag = \File::copy_dir($obj->path, $parent.$dirnname);
				if ( ! $flag)
				{
					$flag = \File::delete_dir($obj->path, $recursive = true);
				}
				else
				{
					\Session::set_flash('error', 'ディレクトリの移動（作成）に失敗しました。');
					\Response::redirect(\Uri::create('flr/move_dir/'.$id));
				}

				if( ! $flag)
				{
					\Session::set_flash('error', 'ディレクトリの移動（削除）に失敗しました。');
					\Response::redirect(\Uri::create('flr/move_dir/'.$id));
				}
			}
			else
			{
				\Session::set_flash('error', 'ディレクトリの移動（削除）に失敗しました。');
				\Response::redirect(\Uri::create('flr/move_dir/'.$id));
			}
		}

		// parent::edit()
		$obj = parent::edit($id);

		// new path
		if (\Input::post() && ! $errors)
		{
			$obj->path = $parent.$dirnname;
			$obj->save();
		}

		// rewrite message
		$success = \Session::get_flash('success');
		if ($success)
		{
			\Session::set_flash('success', "ディレクトリを移動しました。");
		}

		// assign
		$this->template->set_global('title', 'ディレクトリの移動');
	}

	/**
	 * action_permission_dir()
	 * ディレクトリの権限
	 */
	public function action_permission_dir($id = null)
	{
		// parent::edit()
		$obj = parent::edit($id);

		// to load \Model_Flr::_event_after_update() and \Model_Flr::embed_hidden_info().
		// no update cause no load observer_after_update
		if (\Input::post())
		{
			$obj = $obj ? $obj : \Model_Flr::find($id);
			$obj->updated_at = date('Y-m-d H:i:s');
			$obj->save();
		}

		// rewrite message
		// ディレクトリを新規作成すると権限設定に来るので、そのときにはメッセージを上書きしない。
		$success = \Session::get_flash('success');
		if ($success && strpos(\Input::referrer(), 'permission_dir') !== false)
		{
			\Session::set_flash('success', "ディレクトリの権限を変更しました。");
		}

		// assign
		$this->template->set_global('title', 'ディレクトリ権限');
	}

	/**
	 * action_purge_dir()
	 * ディレクトリの削除
	 */
	public function action_purge_dir($id = null)
	{
		$errors = array();

		// create dir
		if (\Input::post())
		{
			$path = \Input::post('path');

			if ( ! file_exists($parent.$dirnname))
			{
				\Session::set_flash('error', '削除すべきディレクトリが存在しません。こういったディレクトリは時々行われる自動同期で修正されます。');
				\Response::redirect(\Uri::create('flr/purge_dir/'.$id));
			}
			elseif ( ! \File::delete_dir($path, $recursive = true))
			{
				\Session::set_flash('error', 'ディレクトリの削除に失敗しました。こういったディレクトリは時々行われる自動同期で修正されます。');
				\Response::redirect(\Uri::create('flr/purge_dir/'.$id));
			}

			// error
			if($errors)
			{
				$obj = parent::purge($id);
				\Session::set_flash('error', $errors);
			}
			// rewrite message
			$success = \Session::get_flash('success');
			if ($success)
			{
				\Session::set_flash('success', "ディレクトリを削除しました。");
			}
		}

		// parent::edit()
		$obj = parent::edit($id);

		// assign
		$this->template->set_global('title', 'ディレクトリ削除');
	}

	/**
	 * action_upload()
	 */
	public function action_upload($id = null)
	{
		$errors = array();

		// upload
		if (\Input::post())
		{
			if (\Input::file())
			{
				$obj = \Model_Flr::find($id, \Model_Flr::authorized_option(array(), 'edit'));

				$config = array(
					'path' => $obj->path,
				);
				
				\Upload::process($config);

if (\Upload::is_valid())
{
\Upload::save();
//  Model_Uploads::add(Upload::get_files());
}

// and process any errors
foreach (\Upload::get_errors() as $file)
{
	$errors[] = $file;
    // $file is an array with all file information,
    // $file['errors'] contains an array of all error occurred
    // each array element is an an array containing 'error' and 'message'
//					$errors[] = 'アップロードに失敗をしました。';
}

			}
			else
			{
				$errors[] = 'アップロードするファイルが選択されていません。';
			}
		}

		// parent::edit()
		$obj = parent::edit($id);

		// new path
		if (\Input::post() && ! $errors)
		{
			$obj->path = $parent.$dirnname;
			$obj->save();
		}

		// error
		if($errors) \Session::set_flash('error', $errors);

		// rewrite message
		$success = \Session::get_flash('success');
		if ($success)
		{
			\Session::set_flash('success', "ファイルをアップロードしました。");
		}

		$this->template->set_global('title', 'ファイルアップロード');
	}

	/**
	 * action_view_file()
	 */
	public function action_view_file($id = null)
	{
		$obj = \Model_Flr::find($id);
		$plain = \Model_Flr::plain_definition('view_file', $obj);

		// view
		$this->set_object($obj);
		$content = \View::forge('flr/view_file');
		$content->set_safe('plain', $plain);
		$this->template->content = $content;
		$this->template->set_global('title', 'ファイル詳細');
	}

	/**
	 * action_edit_file()
	 */
	public function action_edit_file($id = null)
	{
		$obj = parent::edit($id);
		$obj = $obj ? $obj : \Model_Flr::find($id);
		$this->template->set_global('title', 'ファイル編集');
	}

	/**
	 * action_move_file()
	 */
	public function action_move_file($id = null)
	{
		$obj = parent::edit($id);
		$this->template->set_global('title', 'ファイル移動');
	}

	/**
	 * action_purge_file()
	 */
	public function action_purge_file($id = null)
	{
		$obj = parent::edit($id);
		$this->template->set_global('title', 'ファイル削除');
	}

	/**
	 * check_auth()
	 */
	public static function check_auth($path, $writable = false)
	{
		// usergroups
		$usergroups = \Auth::get_groups();

		// always true
		if (in_array('-1', $usergroups) || in_array('-2', $usergroups) ) return true;

		$obj = \Model_Flr::find('first', array('where' => array(array('path', $path))));
		if ( ! $obj) return false;

		// check usergroups
		$allowed_groups = array();
		foreach ($obj->permission_usergroup as $v)
		{
			if ($writable && ! $v->is_writable) continue;
			$allowed_groups[] = $v->usergroup_id;
		}
		$is_allowed = false;
		foreach ($usergroups as $usergroup)
		{
			if (in_array($usergroup, $allowed_groups))
			{
				$is_allowed = true;
				break;
			}
		}
		if ($is_allowed) return true;

		// user_id
		$allowed_users = array();
		foreach ($obj->permission_user as $v)
		{
			if ($writable && ! $v->is_writable) continue;
			$allowed_users[] = $v->user_id;
		}
		$uid = \Auth::get('id');
		return in_array($uid, $allowed_users);
	}
}
