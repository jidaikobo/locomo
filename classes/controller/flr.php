<?php
namespace Locomo;
class Controller_Flr extends \Locomo\Controller_Base
{
	// locomo
	public static $locomo = array(
		'nicename'     => 'ファイラ', // for human's name
		'explanation'  => 'ファイルの閲覧やアップロードを行います。', // for human's explanation
		'main_action'  => 'index_files', // main action
		'main_action_name' => 'ファイル管理', // main action's name
		'main_action_explanation' => 'ファイルのアップロードや、アップロードされたファイルの閲覧を行います。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => false, // true: hide from admin bar
		'order'        => 1030, // order of appearance
		'widgets' =>array(
			array('name' => '共有ダウンロードファイル', 'uri' => '\\Controller_Flr/common_files'),
			array('name' => 'ギャラリー', 'uri' => '\\Controller_Flr/gallery'),
		),
	);

	/**
	 * before()
	 */
	public function before()
	{
		parent::before();

		// check env.
		// dbの存在確認（同期途中のトラブルなどでデータベースが失われていたりする場合の処理）
		require_once(LOCOMOPATH.'migrations/007_create_flrs.php');
		if ( ! \DBUtil::table_exists('lcm_flrs'))
		{
			$obj = new \Fuel\Migrations\Create_Flrs;
			$obj->create_table_flrs();
		}
		if ( ! \DBUtil::table_exists('lcm_flr_permissions'))
		{
			$obj = new \Fuel\Migrations\Create_Flrs;
			$obj->create_table_permissions();
		}

		// ディレクトリの存在確認
		if ( ! file_exists(LOCOMOUPLOADPATH)) throw new \Exception("LOCOMOUPLOADPATH not found. create '".LOCOMOUPLOADPATH."'");
		if (\Str::ends_with(LOCOMOUPLOADPATH, DS)) throw new \Exception("LOCOMOUPLOADPATH must not be terminated with '/'");

		// LOCOMOFLRUPLOADPATH
		defined('LOCOMOFLRUPLOADPATH') or define('LOCOMOFLRUPLOADPATH', LOCOMOUPLOADPATH.DS.'flr');

		// check permission and set default permission
		// ディレクトリパーミッションの確認と、デフォルトパーミッションの設定
		if ( ! file_exists(LOCOMOFLRUPLOADPATH.DS.'.LOCOMO_DIR_INFO'))
		{
			// get default permission from config
			$config = \Config::load('upload');
			$default_permission = \Arr::get($config, 'default_permission', array());
			$default_permission['usergroup'] = @$default_permission['usergroup'] ?: array();
			$default_permission['user'] = @$default_permission['user'] ?: array();

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
				\File::create(LOCOMOFLRUPLOADPATH.DS, '.LOCOMO_DIR_INFO', serialize($arr));
			} catch (\Fuel\Core\InvalidPathException $e) {
				throw new \Fuel\Core\InvalidPathException("'".LOCOMOFLRUPLOADPATH."', cannot create file at this location. アップロードディレクトリの書き込み権限かファイルオーナを確認してください。");
			}
		}
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
		$current_obj = \Model_Flr::find($id);

		// check_auth()
		if ($current_obj && ! static::check_auth($current_obj->path))
		{
			\Session::set_flash('error', "アクセス権のないディレクトリです。");
			\Response::redirect(\Uri::create('flr/index_files/'));
		}

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
					$path = LOCOMOFLRUPLOADPATH.$obj->path;
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
					) 
				);
				\Model_Flr::$_options['order_by'] = array(
					'genre'      => 'asc',
					'created_at' => 'desc'
				);
			}

			// eliminate root dir
			\Model_Flr::$_options['where'][] = array('depth', '!=', '0');

			// span
			if (\Input::get('from')) \Model_Flr::$_options['where'][] = array('created_at', '>=', \Input::get('from'));
			if (\Input::get('to'))   \Model_Flr::$_options['where'][] = array('created_at', '<=', \Input::get('to'));

			// set_paginated_options
			\Model_Flr::set_paginated_options();
			$objs = \Model_Flr::find('all', \Model_Flr::$_options) ;
		}

		// count
		\Pagination::$refined_items = count($objs);

		// view
		$content = \Presenter::forge('flr/index/files');
		$this->template->set_safe('content', $content);

		// search_form
		$this->template->content->set_safe('search_form', $content::search_form('ファイル一覧'));

		// assign
		$content->get_view()->set_safe('breadcrumbs', self::breadcrumbs($current_obj->path));
		$content->get_view()->set('items', $objs);
		$this->template->set_global('title', 'ファイル一覧');

		// set object
		$this->set_object($current_obj);
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
			'order_by' => array(
				array('name', 'ASC'),
			),
		);
		$objs = \Model_Flr::find('all', $option);

		// view
		$content = \View::forge('flr/common_files');
		$content->set('items', $objs);
		$this->template->set_safe('content', $content);
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
		$this->template->set_safe('content', $content);
		$this->template->set_global('title', 'ファイル一覧');
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
					$breadcrumbs[] = \Html::anchor('flr/file/view/'.$tmp->id, urldecode($path));
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
		// ルートディレクトリはアクセスはできる
		$paths = explode('/', $path);
		if ($level == 'read' && count($paths) == 2) return true;

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

		// check first level dir
		$paths = explode('/', $path);
		if (count($paths) < 2) return false; // invalid depth
		$obj = \Model_Flr::find('first', array('where' => array(array('path', "/{$paths[1]}/"))));
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
