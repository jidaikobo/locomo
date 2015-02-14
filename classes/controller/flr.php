<?php
namespace Locomo;
class Controller_Flr extends \Locomo\Controller_Base
{
	// locomo
	public static $locomo = array(
		'nicename'     => 'ファイラ', // for human's name
		'explanation'  => 'ファイルの閲覧やアップロードを行います。', // for human's explanation
		'main_action'  => 'action_index_files', // main action
		'main_action_name' => 'ファイル管理', // main action's name
		'main_action_explanation' => 'ファイルのアップロードや、アップロードされたファイルの閲覧を行います。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => false, // true: hide from admin bar
		'order'        => 1030, // order of appearance
		'widgets' =>array(
			array('name' => '共有ダウンロードファイル', 'uri' => '\\Controller_Flr::action_common_files'),
			array('name' => 'ギャラリー', 'uri' => '\\Controller_Flr::action_gallery'),
		),
	);

	/**
	 * before()
	 */
	public function before()
	{
		parent::before();

		// check env.
		// ディレクトリの存在確認
		if ( ! file_exists(LOCOMOUPLOADPATH)) throw new \Exception("LOCOMOUPLOADPATH not found. create '".LOCOMOUPLOADPATH."'");
		if (\Str::ends_with(LOCOMOUPLOADPATH, DS)) throw new \Exception("LOCOMOUPLOADPATH must not be terminated with '/'");

		// check permission and set default permission
		// ディレクトリパーミッションの確認と、デフォルトパーミッションの設定
		if ( ! file_exists(LOCOMOUPLOADPATH.DS.'.LOCOMO_DIR_INFO'))
		{
			// get default permission from config
			$config = \Config::load('upload');
			$default_permission = \Arr::get($config, 'default_permission', array());

			// readble arr to machine-like arr.
			$group = \Model_Flr::modify_intersects_arr_to_modellike_arr($default_permission['usergroup'], 'usergroup');
			$user = \Model_Flr::modify_intersects_arr_to_modellike_arr($default_permission['user'], 'user');

			// prepare array
			$key = md5('/'); // root array key
			$arr = array(
				$key => array(
					'data' => array(),
					'permission_usergroup' => $group,
					'permission_user' => $user,
				),
			);

			// put file
			try
			{
				\File::create(LOCOMOUPLOADPATH.DS, '.LOCOMO_DIR_INFO', serialize($arr));
			} catch (\Fuel\Core\InvalidPathException $e) {
				throw new \Fuel\Core\InvalidPathException("'".LOCOMOUPLOADPATH."', cannot create file at this location. アップロードディレクトリの書き込み権限かファイルオーナを確認してください。");
			}
		}
	}

	/**
	 * action_common_files()
	 */
	public function action_common_files()
	{
		// hmvc(widget) only
		if ( ! \Request::is_hmvc())
		{
			$page = \Request::forge('sys/404')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 404);
		}

		// current dir
		$option = array(
			'where' => array(
				array('is_sticky', 1),
				array('genre', '<>','image'),
			),
/*
			'order_by' => array(
				array('seq', 'ASC'),
			),
*/
		);
		$objs = \Model_Flr::find('all', $option);

		// view
		$content = \View::forge('flr/common_files');
		$content->set('items', $objs);
		$this->template->content = $content;
		$this->template->set_global('title', 'ファイル一覧');
	}

	/**
	 * action_gallery()
	 */
	public function action_gallery()
	{
		// current dir
		$option = array(
			'where' => array(
				array('is_sticky', 1),
				array('genre', 'image'),
			),
		);
		$objs = \Model_Flr::find('all', $option);

		// view
		$content = \View::forge('flr/gallery');
		$content->set('items', $objs);
		$this->template->content = $content;
		$this->template->set_global('title', 'ファイル一覧');
	}

	/**
	 * action_index_files()
	 */
	public function action_index_files($id = 1, $sync = false)
	{
		if ($sync)
		{
			// dbのupdate直後にsync()するとうまく行かないので、一回画面遷移を経る。
			// 理由はよくわからない :-(
			\Session::keep_flash('success');
			Controller_Flr_Sync::sync();
			\Response::redirect(static::$current_url.$id);
		}

		// current dir
		$current_obj = \Model_Flr::find($id, \Model_Flr::authorized_option(array(), 'index_files'));

		// show current dir items. when search ingnore this.
		// 現在のディレクトリを表示。
		// 検索の場合でもいちおうディレクトリ整合性確認のため走るが、結果は無視する。
		if ($current_obj)
		{
			// children
			$objs = \Model_Flr::get_children($current_obj);

			// dir validation
			$not_found = false;
			if ($current_obj->path != '/' && $objs)
			{
				foreach ($objs as $obj)
				{
					$path = LOCOMOUPLOADPATH.$obj->path;
					if ( ! file_exists($path))
					{
						$not_found = true;
						break;
					}
				}
			}
		}

		// force sync
		if ( ! $current_obj || $not_found)
		{
			if (Controller_Flr_Sync::sync())
			{
				\Session::set_flash('success', "データベースとファイルの状況に矛盾が見つかったので、強制同期をかけました。");
			} else {
				\Session::set_flash('error', "データベースとファイルの矛盾解消のため、強制同期をかけましたが失敗しました。矛盾は解消されていません。物理ファイルの状況を確認してください。");
			}
			\Response::redirect(static::$main_url);
		}

		// search
		// 検索の場合
		if (\Input::get('submit'))
		{
			// free word search
			$all = \Input::get('all') ? '%'.\Input::get('all').'%' : '' ;
			if ($all)
			{
				\Model_Flr::$_options['where'][] = array(
					array('name', 'LIKE', $all),
					'or' => array(
						array('explanation', 'LIKE', $all),
						'or' => array(
							array('path', 'LIKE', $all), 
						)
					) 
				);
			}
	
			// span
			if (\Input::get('from')) \Model_Flr::$_options['where'][] = array('created_at', '>=', \Input::get('from'));
			if (\Input::get('to'))   \Model_Flr::$_options['where'][] = array('created_at', '<=', \Input::get('to'));

			// overwrite objs
			$objs = \Model_Flr::paginated_find();
		}

		// count
		$current = count($objs);

		// view
		$content = \View::forge('flr/index_files');
		$this->template->content = $content;

		// search_form
		$search_form = \Model_Flr::search_form();
		$this->template->content->set_safe('search_form', $search_form);

		// assign
		$content->set_global('current', $current_obj);
		$content->set_safe('breadcrumbs', self::breadcrumbs($current_obj->path));
		$content->set('items', $objs);
		$this->template->set_global('title', 'ファイル一覧');

		// set object
		$this->set_object($current_obj);
	}

	/**
	 * action_upload()
	 */
	public function action_upload($id = null)
	{
		$obj = \Model_Flr::find($id);

		// existence
		if ( ! $obj)
		{
			\Session::set_flash('error', "ディレクトリが見つかりませんでした");
			\Response::redirect(static::$main_url);
		}

		// check_auth
		if ( ! static::check_auth($obj->path, 'upload'))
		{
			\Session::set_flash('error', "ディレクトリに対するアップロード権限がありません。");
			\Response::redirect(static::$main_url);
		}

		// upload
		$errors = array();
		if (\Input::post())
		{
			if (\Input::file())
			{
				// upload
				$fullpath = LOCOMOUPLOADPATH.$obj->path;
				$config = array(
					'path' => $fullpath,
				);
				\Upload::process($config);
				\Upload::register('before', array($this, 'modify_filename'));
				if (\Upload::is_valid())
				{
					\Upload::save();
				}

				// and process any errors
				foreach (\Upload::get_errors() as $file)
				{
					$errors[] = $file['errors'];
					$errors[] = 'アップロードに失敗をしました。';
				}

				// upload succeed
				if ( ! $errors)
				{
					$obj_file = \Model_Flr::forge();
					$name = \Arr::get(\Input::file(), 'upload.name');
					$obj_file->name        = urldecode($name);
					$obj_file->path        = $obj->path.$name;
					$obj_file->is_sticky   = \Input::post('is_sticky');
					$obj_file->ext         = substr($name, strrpos($name, '.') + 1);
					$obj_file->genre       = \Locomo\File::get_file_genre($name);
					$obj_file->is_visible  = \Input::post('is_visible', 1);
					$obj_file->explanation = \Input::post('explanation');
					if ($obj_file->save())
					{
						\Session::set_flash('success', "ファイルをアップロードしました。");
						\Response::redirect(\Uri::create('flr/view_file/'.$obj_file->id));
					} else {
						$errors[] = 'アップロードに失敗をしました。';
						\File::delete($fullpath);
					}

				}
			}
			else
			{
				$errors[] = 'アップロードするファイルが選択されていません。';
			}
		}

		// parent::edit()
		$obj = parent::edit($id);

		// error
		\Session::delete_flash('error'); // delete \Upload::save() generate message
		if($errors) \Session::set_flash('error', $errors);

		$this->template->set_global('title', 'ファイルアップロード');
	}

	/**
	 * modify_filename()
	 */
	public static function modify_filename(&$file)
	{
		$file['filename'] = urlencode($file['filename']);
	}

	/**
	 * action_view_file()
	 */
	public function action_view_file($id = null)
	{
		$obj = \Model_Flr::find($id);

		// existence
		if ( ! $obj)
		{
			\Session::set_flash('error', "ファイル／ディレクトリが見つかりませんでした");
			\Response::redirect(static::$main_url);
		}

		// check_auth
		if ( ! static::check_auth($obj->path, 'read'))
		{
			\Session::set_flash('error', "ファイル閲覧の権利がありません。");
			\Response::redirect(static::$main_url);
		}

		$plain = \Model_Flr::plain_definition('view_file', $obj)->build_plain();

		// view
		$this->set_object($obj);
		$content = \View::forge('flr/view_file');
		$content->set_safe('plain', $plain);
		$content->set_safe('breadcrumbs', self::breadcrumbs($obj->path));
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
	 * _action_move_file()
	 * 実装見合わせ
	 */
	public function _action_move_file($id = null)
	{
		$obj = parent::edit($id);
		$this->template->set_global('title', 'ファイル移動');
	}

	/**
	 * action_purge_file()
	 */
	public function action_purge_file($id = null)
	{
		$this->action_purge_dir($id);
		$this->template->set_global('title', 'ファイル削除');
	}

	/**
	 * action_dl()
	 * at default, this action is opened to guest!
	 */
	public function action_dl()
	{
		// path
		$path = \Model_Flr::enc_url(\Input::get('p'));
		$obj = Model_Flr::find('first', array('where' => array(array('path', $path))));

		// 404
		$page = \Request::forge('sys/404')->execute();
		$this->template->set_safe('content', $page);
		if ( ! $obj)
		{
			return new \Response($page, 404);
		}

		// check_auth
		// for security, always return 404
		// 厳密には403を返すべきかもしれないが、ファイル実体があることを判別させてしまうので、404を返す
		if ( ! static::check_auth($obj->path, 'read'))
		{
			return new \Response($page, 404);
		}

		// Download or view
		$fullpath = LOCOMOUPLOADPATH.$obj->path;
		if (\Locomo\File::get_file_genre($fullpath) != 'image' || \Input::get('dl'))
		{
			try
			{
				\File::download($fullpath, $obj->name);
			} catch (\Fuel\Core\InvalidPathException $e) {
				return new \Response($page, 404);
			}
		} else {
			try
			{
				\File::download($fullpath, null, $obj->mimetype, null, false, 'inline');
			} catch (\Fuel\Core\InvalidPathException $e) {
				return new \Response($page, 404);
			}
		}

		exit();
	}

	/**
	 * breadcrumbs()
	 */
	public static function breadcrumbs($target_path)
	{
		$paths = array_reverse(array_filter(explode('/', $target_path)));
		$tmp_path = $target_path;
		$breadcrumbs = array();
		foreach ($paths as $path)
		{
			$tmp = Model_Flr::find('first', array('where' => array(array('path', $tmp_path))));
			if ($tmp)
			{
				if ($tmp->genre == 'dir')
				{
					$breadcrumbs[] = \Html::anchor('flr/index_files/'.$tmp->id, urldecode($path));
				} else {
					$breadcrumbs[] = \Html::anchor('flr/view_file/'.$tmp->id, urldecode($path));
				}
			}
			$tmp_path = dirname($tmp_path).DS;
		}
		$breadcrumbs = array_reverse($breadcrumbs);

		$html = '';
		$html.= '<div class="lcm_flr_breadcrumbs">現在位置：';
		$html.= \Html::anchor('flr/index_files/', 'TOP').DS.join('/', $breadcrumbs);
		$html.= '</div><!--/.lcm_flr_breadcrumbs-->';

		return $html;
	}

	/**
	 * check_auth()
	 */
	public static function check_auth($path, $level = 'read')
	{
		// rights
		$rights = array(
		 'read'       => 1,
		 'upload'     => 2,
		 'create_dir' => 3,
		 'rename_dir' => 4,
		 'purge_dir'  => 5,
		);
		if ( ! array_key_exists($level, $rights)) return false;

		// usergroups
		$usergroups = \Auth::get_groups();

		// always true
		if (in_array('-1', $usergroups) || in_array('-2', $usergroups) ) return true;

		// when file, check parent dir
		$fullpath = LOCOMOUPLOADPATH.$path;
		$path = is_dir($fullpath) ? $path : rtrim(dirname($path),DS).DS;
		$obj = \Model_Flr::find('first', array('where' => array(array('path', $path))));
		if ( ! $obj) return false;

		// check usergroups
		$allowed_groups = array();
		foreach ($obj->permission_usergroup as $v)
		{
			if ($v->access_level < $rights[$level]) continue;
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
			if ($v->access_level < $rights[$level]) continue;
			$allowed_users[] = $v->user_id;
		}
		$uid = \Auth::get('id');
		return in_array($uid, $allowed_users);
	}
}
