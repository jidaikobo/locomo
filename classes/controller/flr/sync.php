<?php
namespace Locomo;
class Controller_Flr_Sync extends Controller_Flr
{
	// locomo
	public static $locomo = array(
		'no_acl' => true,
	);

	/**
	 * action_sync()
	 * 現状のディレクトリと同期。データベースは現状のディレクトリにあわせて刷新される
	 */
	public function action_sync()
	{
		// sync
		if (\Input::post())
		{
			if (static::sync())
			{
				\Session::set_flash('success', "データベースとディレクトリを同期しました。");
			} else {
				\Session::set_flash('error', "データベースとディレクトリの同期に失敗しました。物理ファイルの状況を確認してください。");
			}
		}

		// view
		$form = \Model_Flr::sync_definition();
		$content = \View::forge('defaults/edit');
		$content->set_safe('breadcrumbs', self::breadcrumbs(LOCOMOUPLOADPATH));
		$content->set_safe('form', $form);
		$this->template->content = $content;
		$this->template->set_global('title', '同期');
	}

	/**
	 * sync()
	 */
	public static function sync()
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

		// eliminate invalid filenames
		foreach ($items as $k => $fullpath)
		{
			if ($fullpath == LOCOMOUPLOADPATH.DS) continue; //root dir
			$enc_name = \Model_Flr::enc_url($fullpath);

			// if same name exists
			// エンコード名が改名後と同じ項目があるときにはエラーを返す
			if (file_exists($enc_name) && ! preg_match("/^[%a-zA-Z0-9\._-]+/", basename($fullpath)))
			{
				$errors = array(
					'同期は不完全に終わりました。',
					"'".urldecode(basename($fullpath))."'は、エンコード後の名前が同じものがあるので、名称を変更してください。",
				);
				\Session::set_flash('error', $errors);
				\Response::redirect(\Uri::create('flr/sync/sync'));
			}

			// if not exist. it maybe already enced.
			// ファイルが存在しない場合はすでにエンコードされているので、エンコードする
			if ( ! file_exists($fullpath))
			{
				$fullpath = \Model_Flr::enc_url(dirname($fullpath)).DS.basename($fullpath);
			}

			try
			{
				\File::rename($fullpath, $enc_name);
			} catch (\Fuel\Core\PhpErrorException $e) {
				$errors = array(
					'同期は不完全に終わりました。',
					"'".urldecode(basename($fullpath))."'は、パーミッションが妥当でありませんシステム管理者に修正を依頼してください。",
				);
				\Session::set_flash('error', $errors);
				\Response::redirect(\Uri::create('flr/sync/sync'));
			}
		}

		// reload
		$items = \Util::get_file_list(LOCOMOUPLOADPATH);

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
			$obj->mimetype    = is_dir($fullpath) ? '' : \File::file_info($fullpath)['mimetype'] ;
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
}