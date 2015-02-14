<?php
namespace Locomo;
class Controller_Flr_Dir extends Controller_Flr
{
	// locomo
	public static $locomo = array(
		'no_acl' => true,
	);

	/**
	 * action_create()
	 * ディレクトリの作成
	 */
	public function action_create($id = null)
	{
		// check dir existence before db connection
		// postがあったらまず物理ディレクトリを確認する
		if (\Input::post())
		{
			$parent =  \Input::post('parent', '/');
			$dirnname = \Input::post('name');
			$path = \Model_Flr::enc_url($parent.$dirnname).DS;
			$tmp_obj = \Model_Flr::find('first', array('where' => array(array('path', $path))));

			if ($tmp_obj && file_exists(LOCOMOUPLOADPATH.$path))
			{
				\Session::set_flash('error', 'そのディレクトリは既に存在します。');
				\Response::redirect(\Uri::create('flr/dir/create/'.$id));
			}
		}

		// parent::edit()
		$this->model_name = '\\Model_Flr';
		$edit_obj = parent::edit();

		// at database: not found, save: success, file existing
		// データベースには存在しなかったが、物理ディレクトリが存在していて、データベースへの保存が成功したとき。
		if ( $edit_obj && ! $tmp_obj && file_exists(LOCOMOUPLOADPATH.$path))
		{
			\Session::set_flash('success', '物理ディレクトリは存在していますが、データベース上にディレクトリが存在しなかったので、物理ディレクトリを作成せず、データベースのみをアップデートしました。');
			\Response::redirect(\Uri::create('flr/dir/create/'.$id));
		}

		// create physical dir
		// parent::edit()がobjectを返したらDBアップデートが成功したので、ディレクトリを作る
		if (is_object($edit_obj))
		{
			// try to create dir
			if ( ! \File::create_dir(LOCOMOUPLOADPATH.$parent, \Model_Flr::enc_url($dirnname)))
			{
				// 失敗したのでデータベースから削除
				$edit_obj->purge();
				\Session::set_flash('error', 'ディレクトリの新規作成に失敗しました。');
				\Response::redirect(\Uri::create('flr/dir/create/'.$id));
			} else {
				\Session::set_flash('success', "ディレクトリを新規作成しました。");
				$pobj = \Model_flr::get_parent($edit_obj);
				$id = is_object($pobj) ? $pobj->id : 1 ;//root
				\Response::redirect(\Uri::create('flr/dir/index_files/'.$id));
			}
		}

		// assign
		$parent = \Model_Flr::find($id);
		$this->template->content->set_safe('breadcrumbs', self::breadcrumbs($parent->path));
		$this->template->set_global('title', 'ディレクトリ作成');
	}

	/**
	 * action_edit()
	 * ディレクトリのメモ欄の編集
	 */
	public function action_edit($id = null)
	{
		$obj = \Model_Flr::find($id);

		// existence
		if ( ! $obj)
		{
			\Session::set_flash('error', "ファイル／ディレクトリが見つかりませんでした");
			\Response::redirect(static::$main_url);
		}

		// check_auth
		if ( ! static::check_auth($obj->path, 'create_dir'))
		{
			\Session::set_flash('error', "ディレクトリの編集をする権利がありません。");
			\Response::redirect(static::$main_url);
		}

		// parent::edit()
		$this->model_name = '\\Model_Flr';
		$edit_obj = parent::edit($id);

		if ($edit_obj)
		{
			\Response::redirect(static::$current_url.$id);
		}

		$this->template->content->set_safe('breadcrumbs', self::breadcrumbs($obj->path));
		$this->template->set_global('title', 'ディレクトリの編集');
	}

	/**
	 * action_rename()
	 * ディレクトリのリネーム
	 */
	public function action_rename($id = null, $sync = false)
	{
		if ($sync)
		{
			// dbのupdate直後にsync()するとうまく行かないので、一回画面遷移を経る。
			// 理由はよくわからない :-(
			\Session::keep_flash('success');
			\Controller_Flr_Sync::sync();
			\Response::redirect(static::$current_url.$id);
		}

		$obj = \Model_Flr::find($id, \Model_Flr::authorized_option(array(), 'edit'));

		// not exist
		if ( ! $obj)
		{
			\Session::set_flash('error', "ディレクトリが存在しません。");
			\Response::redirect(static::$main_url);
		}

		// root directory
		if (LOCOMOUPLOADPATH.$obj->path == LOCOMOUPLOADPATH.DS)
		{
			\Session::set_flash('error', "基底ディレクトリは名称変更できません。");
			\Response::redirect(static::$main_url);
		}

		// check_auth
		if ( ! static::check_auth($obj->path, 'rename_dir'))
		{
			\Session::set_flash('error', "ディレクトリの名称を変更する権利がありません。");
			\Response::redirect(static::$main_url);
		}

		// rename dir
		if (\Input::post())
		{
			$prev_name = $obj->name;
			$new_name = \Input::post('name');
			$parent = LOCOMOUPLOADPATH.dirname($obj->path);

			// rename
			if ($prev_name != $new_name)
			{
				$prev = \Model_Flr::enc_url($parent.$prev_name);
				$new  = \Model_Flr::enc_url($parent.$new_name);

				// unicode normalized issue
				// 濁音付き文字など、同じ文字を別物と判定することがあるが、有効手段がない。
				// \Normalizerクラスがあればよいが、かならずしも存在しないので。
				if (file_exists($new))
				{
					\Session::set_flash('error', "同じ階層に同じ名前のディレクトリが既に存在します。");
					\Response::redirect(\Uri::create('flr/dir/rename/'.$obj->id));
				}

				// try to rename
				// 解明に失敗するということはデータベースとディレクトリの状態に矛盾があるということ
				try
				{
					$rename = \File::rename_dir($prev, $new);
				} catch (\Fuel\Core\PhpErrorException $e) {
					Controller_Flr_Sync::sync();
					\Session::set_flash('error', "データベースとファイルの状況に矛盾が見つかったので、強制同期をかけました。");
					\Response::redirect(static::$main_url);
				}

				// failed to rename
				// その他の理由による失敗
				if( ! $rename)
				{
					\Session::set_flash('error', "ディレクトリのリネームに失敗しました。");
					\Response::redirect(\Uri::create('flr/dir/rename/'.$obj->id));
				}
			}
		}

		// parent::edit()
		$this->model_name = '\\Model_Flr';
		$edit_obj = parent::edit($id);

		// rewrite message
		$success = \Session::get_flash('success');
		if ($edit_obj && $success)
		{
			\Session::set_flash('success', "ディレクトリをリネームしました。");
			\Response::redirect(\Uri::create('flr/dir/rename/'.$obj->id.DS.'sync'));
		} else {
			$this->template->content->set_safe('breadcrumbs', self::breadcrumbs($obj->path));
		}

		// assign
		$this->template->set_global('title', 'ディレクトリリネーム');
	}

	/**
	 * action_permission()
	 * ディレクトリの権限
	 */
	public function action_permission($id = null)
	{
		$this->model_name = '\\Model_Flr';
		$obj = \Model_Flr::find($id);

		// check_auth
		if ( ! static::check_auth($obj->path, 'create_dir'))
		{
			\Session::set_flash('error', "ディレクトリの権限を変更する権利がありません。");
			\Response::redirect(static::$main_url);
		}

		// parent::edit()
		$edit_obj = parent::edit($id);

		// to load \Model_Flr::_event_after_update() and \Model_Flr::embed_hidden_info().
		// no update cause no load observer_after_update
		if (\Input::post())
		{
			$edit_obj = $edit_obj ? $edit_obj : \Model_Flr::find($id);
			$edit_obj->updated_at = date('Y-m-d H:i:s');
			$edit_obj->save();
		} else {
			$this->template->content->set_safe('breadcrumbs', self::breadcrumbs($obj->path));
		}

		// rewrite message
		$success = \Session::get_flash('success');
		if ($success && strpos(\Input::referrer(), 'permission') !== false)
		{
			\Session::set_flash('success', "ディレクトリの権限を変更しました。");
		}

		// assign
		$this->template->set_global('title', 'ディレクトリ権限');
	}

	/**
	 * action_purge()
	 * ファイル／ディレクトリの削除
	 */
	public function action_purge($id = null)
	{
		$this->model_name = '\\Model_Flr';
		$obj = \Model_Flr::find($id);

		// existence
		if ( ! $obj)
		{
			\Session::set_flash('error', "ファイル／ディレクトリが見つかりませんでした。");
			\Response::redirect(static::$main_url);
		}

		// root dir
		if ($obj->id == 1)
		{
			\Session::set_flash('error', "基底ディレクトリは削除できません。");
			\Response::redirect(static::$main_url);
		}

		// check_auth
		if ( ! static::check_auth($obj->path, 'purge_dir'))
		{
			\Session::set_flash('error', "ディレクトリを削除する権利がありません。");
			\Response::redirect(static::$main_url);
		}

		// create dir
		if (\Input::post())
		{
			$is_error = false;
			$path = \Model_Flr::enc_url(LOCOMOUPLOADPATH.\Input::post('path'));
			$target = \Model_Flr::find($id);
			$parent = \Model_Flr::get_parent($target);

			if ( ! file_exists($path))
			{
				$is_error = true;
				\Session::set_flash('error', '削除すべきファイル／ディレクトリが存在しません。');
			}

			if (is_dir($path) && ! \File::delete_dir($path, $recursive = true))
			{
				$is_error = true;
				\Session::set_flash('error', 'ディレクトリの削除に失敗しました。');
			}

			if (file_exists($path) && ! \File::delete($path)) {
				$is_error = true;
				\Session::set_flash('error', 'ファイルの削除に失敗しました。');
			}

			// purge
			if ( ! $is_error)
			{
				try {
					$target->purge(null, true);
					\Session::set_flash('success', "ファイル／ディレクトリを削除しました。");
				}
				catch (\Exception $e) {
					// relation先で何かエラーが出るが、現状どうしようもないので、このまま進行
				}
/*
				if ($target->purge(null, true))
				{
					\Session::set_flash('success', "ファイル／ディレクトリを削除しました。");
				} else {
					\Session::set_flash('error', "ファイル／ディレクトリの削除に失敗しました。");
				}
*/
			}
			\Response::redirect(\Uri::create('flr/dir/index_files/'.$parent->id.'/sync'));
		}

		// parent::edit()
		$edit_obj = parent::edit($id);

		// assign
		$this->template->content->set_safe('breadcrumbs', self::breadcrumbs($obj->path));
		$this->template->set_global('title', 'ディレクトリ削除');
	}

	/**
	 * action_move()
	 * ディレクトリの移動。pending. 実装検討中
	 */
	public function _action_move($id = null)
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
					\Response::redirect(\Uri::create('flr/dir/move/'.$id));
				}

				if( ! $flag)
				{
					\Session::set_flash('error', 'ディレクトリの移動（削除）に失敗しました。');
					\Response::redirect(\Uri::create('flr/dir/move/'.$id));
				}
			}
			else
			{
				\Session::set_flash('error', 'ディレクトリの移動（削除）に失敗しました。');
				\Response::redirect(\Uri::create('flr/dir/move/'.$id));
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
}
