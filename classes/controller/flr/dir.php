<?php
namespace Locomo;
class Controller_Flr_Dir extends Controller_Flr
{
	// locomo
	public static $locomo = array(
		'main_controller'  => '\Controller_Flr',
		'no_acl' => true,
	);

	/**
	 * action_create()
	 * ディレクトリの作成
	 */
	public function action_create($id = null)
	{
		// parent::edit()
		$this->model_name = '\\Model_Flr';
		$edit_obj = parent::edit(null, $is_redirect = false);

		// check dir existence before db connection
		// postがあったらまず物理ディレクトリを確認する
		if (\Input::post())
		{
			$parent =  \Input::post('parent', '/');
			$dirnname = \Input::post('name');
			$path = \Model_Flr::enc_url($parent.$dirnname).DS;
			$tmp_obj = \Model_Flr::find('first', array('where' => array(array('path', $path))));
			// ディレクトリがすでに存在している場合
			if (file_exists(LOCOMOFLRUPLOADPATH.$path) && $dirnname !== '/')
			{
				\Session::delete_flash('success');
				\Session::set_flash('error', 'そのディレクトリは既に存在します。');
				if ($edit_obj && $tmp_obj)
				{
					// 失敗したのでデータベースから削除
					$edit_obj->delete(null, true);
				}
				\Response::redirect(\Uri::create('flr/dir/create/'.$id));
			}

			// at database: not found, save: success, file existing
			// データベースには存在しなかったが、物理ディレクトリが存在していて、データベースへの保存が成功したとき。
			if ($edit_obj && ! $tmp_obj && file_exists(LOCOMOFLRUPLOADPATH.$path))
			{
				\Session::delete_flash('success');
				\Session::set_flash('success', '物理ディレクトリは存在していますが、データベース上にディレクトリが存在しなかったので、物理ディレクトリを作成せず、データベースのみをアップデートしました。');
				\Response::redirect(\Uri::create('flr/dir/create/'.$id));
			}

			// create physical dir
			// parent::edit()がobjectを返したらDBアップデートが成功したので、ディレクトリを作る
			if (is_object($edit_obj))
			{
				// try to create dir
				if ( ! \File::create_dir(LOCOMOFLRUPLOADPATH.$parent, \Model_Flr::enc_url($dirnname)))
				{
					// 失敗したのでデータベースから削除
					$edit_obj->delete_self();
					\Session::set_flash('error', 'ディレクトリの新規作成に失敗しました。');
					\Response::redirect(\Uri::create('flr/dir/create/'.$id));
				} else {
					//新規作成の結果、最上層ディレクトリであれば、パーミッション編集に画面遷移する
					if ($edit_obj->depth == 1)
					{
						\Session::set_flash('success', '引き続いてディレクトリへのアクセス権限の設定をしてください。');
						\Response::redirect(\Uri::create('flr/dir/permission/'.$edit_obj->id));
					}

					// 最上層ディレクトリでないので、ディレクトリ一覧へ遷移
					\Session::set_flash('success', "ディレクトリを新規作成しました。");
					$pobj = \Model_flr::get_parent($edit_obj);
					$id = is_object($pobj) ? $pobj->id : 1 ;//root
					\Response::redirect(\Uri::create('flr/index_files/'.$id));
				}
			}
		}

		// assign
		$parent = \Model_Flr::find($id);
		if ( ! $parent)
		{
			\Response::redirect(\Uri::create('flr/index_files/'));
		}

		$this->template->content->set_safe('breadcrumbs', self::breadcrumbs($parent->path));
		$this->template->set_global('title', 'ディレクトリ作成');
	}

	/**
	 * action_edit()
	 * ディレクトリの編集
	 */
	public function action_edit($id = null)
	{
		$obj = \Model_Flr::find($id);

		// existence
		if ( ! $obj || $obj->genre != 'dir')
		{
			\Session::set_flash('error', "ディレクトリが見つかりませんでした");
			\Response::redirect(dirname(static::$base_url).DS.'index_files');
		}

		// check_auth
		if ( ! static::check_auth($obj->path, 'create_dir'))
		{
			\Session::set_flash('error', "ディレクトリの編集をする権利がありません。");
			\Response::redirect(dirname(static::$base_url).DS.'index_files');
		}

		// 保存前にファイルに対して物理的変化（改名や移動）を適用
		// いずれもルートディレクトリは対象外
		if (\Input::post() && $obj->path != '/')
		{
			// ディレクトリ名称変更の場合
			$prev_name = $obj->name;
			$new_name = \Input::post('name');
			$parent = LOCOMOFLRUPLOADPATH.rtrim(dirname($obj->path), DS).DS;
			$post_parent = LOCOMOFLRUPLOADPATH.rtrim(\Input::post('parent', DS), DS).DS;

			// path
			$prev  = \Model_Flr::enc_url($parent.$prev_name);
			$new   = \Model_Flr::enc_url($parent.$new_name);
			$moved = \Model_Flr::enc_url($post_parent.$new_name);

			// rename
			if ($prev_name != $new_name)
			{
				// unicode normalized issue
				// 濁音付き文字など、同じ文字を別物と判定することがあるが、有効手段がない。
				// \Normalizerクラスがあればよいが、かならずしも存在しないので。
				if (file_exists($new))
				{
					\Session::set_flash('error', "同じ階層に同じ名前のディレクトリが既に存在します。");
					\Response::redirect(static::$current_url.$obj->id);
				}

				// try to rename
				// 改名に失敗するということはデータベースとディレクトリの状態に矛盾があるということ
				try
				{
					$rename = \File::rename_dir($prev, $new);
					\Session::set_flash('success', "ディレクトリの名称を変更しました。");
				} catch (\Fuel\Core\PhpErrorException $e) {
					Controller_Flr_Sync::sync();
					\Session::set_flash('error', "データベースとディレクトリの状況に矛盾が見つかったので、強制同期をかけました。");
					\Response::redirect(static::$current_url.$obj->id);
				}

				// failed to rename
				// その他の理由による失敗
				if( ! $rename)
				{
					\Session::set_flash('error', "ディレクトリのリネームに失敗しました。");
					\Response::redirect(static::$current_url.$obj->id);
				}
			}

			// フォルダ移動
			if ($new != $moved)
			{
				// 移動先に同名のフォルダがある場合
				if (file_exists($moved))
				{
					\Session::set_flash('error', "移動先に同じ名前のディレクトリが既に存在します。");
					\Response::redirect(static::$current_url.$obj->id);
				}

				// try to move
				try
				{
					$move = \File::rename_dir($new, $moved);
					\Session::set_flash('success', "ディレクトリを移動しました。");
				} catch (\Fuel\Core\PhpErrorException $e) {
					Controller_Flr_Sync::sync();
					\Session::set_flash('error', "データベースとディレクトリの状況に矛盾が見つかったので、強制同期をかけました。");
					\Response::redirect(static::$current_url.$obj->id);
				}

				// failed to move
				// その他の理由による失敗
				if( ! $move)
				{
					\Session::set_flash('error', "ディレクトリの移動に失敗しました。");
					\Response::redirect(static::$current_url.$obj->id);
				}
			}
		}

		// parent::edit()
		$this->model_name = '\\Model_Flr';
		$edit_obj = parent::edit($id, $is_redirect = false);

		// 編集を終え、名称変更やフォルダ移動を行っていた場合
		



		// 編集を終えたらとりあえずパーミッションに遷移する。そこではsyncも行う
		if (\Input::post() && $edit_obj)
		{
			\Response::redirect(\Uri::create('flr/dir/permission/'.$obj->id));
		}

		$this->template->content->set_safe('breadcrumbs', self::breadcrumbs($obj->path));
		$this->template->set_global('title', 'ディレクトリの編集');
	}

	/**
	 * action_permission()
	 * ディレクトリの権限
	 */
	public function action_permission($id = null, $sync = false)
	{
		$this->model_name = '\\Model_Flr';
		$obj = \Model_Flr::find($id);
		\Session::keep_flash('success');

		if ($sync)
		{
			// dbのupdate直後にsync()するとうまく行かないので、一回画面遷移を経る。
			// 理由はよくわからない :-(
			\Controller_Flr_Sync::sync();

			// 通常の編集画面へ
			if ($obj)
			{
				// syncしているので、取得し直す
				$obj = \Model_Flr::find('first', array('where' => array(array('path', $obj->path))));
				\Response::redirect(static::$current_url.$obj->id);
			}
			else
			{
			\Response::redirect(\Uri::create('flr/index_files/'));
			}
		}

		// depth
		// パーミッションの対象はルートと最上層ディレクトリのみ
		if ($obj->depth > 1)
		{
			\Response::redirect(\Uri::create('flr/dir/edit/'.$obj->id));
		}

		// check_auth
		if ( ! static::check_auth($obj->path, 'create_dir') || $obj->genre != 'dir')
		{
			\Session::set_flash('error', "ディレクトリの権限を変更する権利がありません。");
			\Response::redirect(dirname(static::$base_url).DS.'index_files');
		}

		// parent::edit()
		$this->_content_template = 'flr/dir/permission';
		$edit_obj = parent::edit($id, $is_redirect = false);

		// to load \Model_Flr::_event_after_update().
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
		if (\Input::post() && $edit_obj && $success && strpos(\Input::referrer(), 'permission') !== false)
		{
			\Session::set_flash('success', "ディレクトリの権限を変更しました。");
			return \Response::redirect(static::$base_url.'permission/'.$edit_obj->id.'/sync/1');
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
		if ( ! $obj || $obj->genre != 'dir')
		{
			\Session::set_flash('error', "ディレクトリが見つかりませんでした。");
			\Response::redirect(dirname(static::$base_url).DS.'index_files');
		}

		// root dir
		if ($obj->id == 1)
		{
			\Session::set_flash('error', "基底ディレクトリは削除できません。");
			\Response::redirect(dirname(static::$base_url).DS.'index_files');
		}

		// check_auth
		if ( ! static::check_auth($obj->path, 'purge_dir'))
		{
			\Session::set_flash('error', "ディレクトリを削除する権利がありません。");
			\Response::redirect(dirname(static::$base_url).DS.'index_files');
		}

		// create dir
		if (\Input::post())
		{
			$is_error = false;
			$path = \Model_Flr::enc_url(LOCOMOFLRUPLOADPATH.\Input::post('path'));
			$target = \Model_Flr::find($id);
			$parent = \Model_Flr::get_parent($target);

			if ( ! file_exists($path))
			{
				$is_error = true;
				\Session::set_flash('error', '削除すべきディレクトリが存在しません。');
			}

			if (is_dir($path) && ! \File::delete_dir($path, $recursive = true))
			{
				$is_error = true;
				\Session::set_flash('error', 'ディレクトリの削除に失敗しました。');
			}

			// purge
			if ( ! $is_error)
			{
				try {
					$target->purge(null, true);
					\Session::set_flash('success', "ディレクトリを削除しました。");
				}
				catch (\Exception $e) {
					// relation先で何かエラーが出るが、現状どうしようもないので、このまま進行
				}
/*
				if ($target->purge(null, true))
				{
					\Session::set_flash('success', "ディレクトリを削除しました。");
				} else {
					\Session::set_flash('error', "ディレクトリの削除に失敗しました。");
				}
*/
			}
			\Response::redirect(\Uri::create('flr/dir/index_files/'.$parent->id.'/sync'));
		}

		// parent::edit()
		$this->_content_template = 'flr/dir/purge';
		$edit_obj = parent::edit($id);

		// assign
		$this->template->content->set_safe('breadcrumbs', self::breadcrumbs($obj->path));
		$this->template->set_global('title', 'ディレクトリ削除');
	}
}
