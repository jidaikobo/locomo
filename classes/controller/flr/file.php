<?php
namespace Locomo;
class Controller_Flr_File extends Controller_Flr
{
	// locomo
	public static $locomo = array(
		'no_acl' => true,
	);

	/**
	 * action_upload()
	 */
	public function action_upload($id = null)
	{
		$this->model_name = '\\Model_Flr';
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
					$errors[] = 'アップロードに失敗をしました。';
					foreach ($file['errors'] as $v)
					{
						$errors[] = $v['message'];
					}
					\Session::set_flash('error', $errors);
					\Response::redirect(\Uri::create('flr/file/view/'.$obj->id));
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
						\Response::redirect(\Uri::create('flr/file/view/'.$obj_file->id));
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
		$this->_content_template = 'flr/file/upload';
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
	 * action_view()
	 */
	public function action_view($id = null)
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

		// set_object
		$this->set_object($obj);

		// presenter
		$content = \Presenter::forge('flr/file/view');

		// view
		$content->set_safe('plain', $content::plain($obj));
		$content->set_safe('breadcrumbs', self::breadcrumbs($obj->path));
		$this->template->content = $content;
		$this->template->set_global('title', 'ファイル詳細');
	}

	/**
	 * action_edit()
	 */
	public function action_edit($id = null)
	{
		$this->model_name = '\\Model_Flr';
		$obj = parent::edit($id);
		$obj = $obj ? $obj : \Model_Flr::find($id);
		$this->template->set_global('title', 'ファイル編集');
	}

	/**
	 * action_purge()
	 */
	public function action_purge($id = null)
	{
		$this->model_name = '\\Model_Flr';
		$obj = \Model_Flr::find($id);

		// existence
		if ( ! $obj)
		{
			\Session::set_flash('error', "ファイルが見つかりませんでした。");
			\Response::redirect(static::$main_url);
		}

		// check_auth
		if ( ! static::check_auth($obj->path, 'upload'))
		{
			\Session::set_flash('error', "ファイルを削除する権利がありません。");
			\Response::redirect(static::$main_url);
		}

		// purge
		if (\Input::post())
		{
			$is_error = false;
			$path = \Model_Flr::enc_url(LOCOMOUPLOADPATH.\Input::post('path'));
			$target = \Model_Flr::find($id);
			$parent = \Model_Flr::get_parent($target);

			if ( ! file_exists($path) || ! is_file($path))
			{
				$is_error = true;
				\Session::set_flash('error', '削除すべきファイルが存在しません。');
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
					\Session::set_flash('success', "ファイルを削除しました。");
				}
				catch (\Exception $e) {
					// relation先で何かエラーが出るが、現状どうしようもないので、このまま進行
				}
/*
				if ($target->purge(null, true))
				{
					\Session::set_flash('success', "ファイルを削除しました。");
				} else {
					\Session::set_flash('error', "ファイルの削除に失敗しました。");
				}
*/
			}
			\Response::redirect(\Uri::create('flr/dir/index_files/'.$parent->id.'/sync'));
		}

		// parent::edit()
		$edit_obj = parent::edit($id);

		// assign
		$this->template->content->set_safe('breadcrumbs', self::breadcrumbs($obj->path));
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
				$filename = \Locomo\Browser::getIEVersion() ? mb_convert_encoding($obj->name, 'sjis-win', 'UTF-8') : $obj->name;
				\File::download($fullpath, $filename);
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
}
