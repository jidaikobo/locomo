<?php
namespace Locomo;
class Controller_Flr extends \Locomo\Controller_Base
{
	// locomo
	public static $locomo = array(
		'nicename'     => 'ファイル', // for human's name
		'explanation'  => 'ファイルの閲覧やアップロードを行います。', // for human's explanation
		'main_action'  => 'index_admin', // main action
		'main_action_name' => 'ファイル管理', // main action's name
		'main_action_explanation' => 'アップロードされたファイルの閲覧を行います。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => true, // true: hide from admin bar
		'order'        => 1030, // order of appearance
		'widgets' =>array(
		),
	);

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		if (\Input::get('from')) \Model_Usr::$_conditions['where'][] = array('created_at', '>=', \Input::get('from'));
		if (\Input::get('to'))   \Model_Usr::$_conditions['where'][] = array('created_at', '<=', \Input::get('to'));
		parent::index_admin();
	}

	/**
	 * sync()
	 */
	protected function sync()
	{
//ディレクトリの状況をデータベースに反映
	}

	/**
	 * action_create_dir()
	 * ディレクトリの作成
	 */
	public function action_create_dir()
	{
		$this->model_name = '\Model_Flr_Dir' ;
		$errors = array();

		// create dir
		if (\Input::post())
		{
			$parent =  \Input::post('parent', LOCOMOUPLADPATH);
			$dirnname = \Input::post('name');

			if (file_exists($parent.$dirnname))
			{
				$errors[] = 'そのディレクトリは既に存在します。';
			}
			elseif ( ! \File::create_dir($parent, \Input::post('name')))
			{
				$errors[] = 'ディレクトリの新規作成に失敗しました。';
			}
		}

		// parent::edit()
		if ( ! \Input::post() || (\Input::post() && ! $errors))
		{
			$obj = parent::edit();
			// path
			if (\Input::post() && $obj)
			{
				$obj->path = dirname($obj->path).DS.$dirnname;
				$obj->save();
				static::$redirect = 'flr/rename_dir/'.$obj->id;
			}
		}

		// error
		if($errors) \Session::set_flash('error', $errors);

		// assign
		$this->template->set_global('title', 'ディレクトリ作成');
	}

	/**
	 * action_rename_dir()
	 * ディレクトリのリネーム
	 */
	public function action_rename_dir($id = null)
	{
		$this->model_name = '\Model_Flr_Dir' ;
		$model = $this->model_name;
		$errors = array();

		// rename dir
		if (\Input::post())
		{
			$obj = $model::find($id, $model::authorized_option(array(), 'edit'));
			$prev_name = $obj->name;
			$parent =  \Input::post('parent', LOCOMOUPLADPATH);
			$dirnname = \Input::post('name');

			// rename
			if ($prev_name != $dirnname)
			{
				\File::rename($obj->path, dirname($obj->path).DS.$dirnname);
			}
		}

		// parent::edit()
		if ( ! \Input::post() || (\Input::post() && ! $errors))
		{
			parent::edit();

			// new path
			if (\Input::post())
			{
				$obj->path = dirname($obj->path).DS.$dirnname;
				$obj->save();
			}
		}

		// error
		if($errors) \Session::set_flash('error', $errors);

		// assign
		$this->template->set_global('title', 'ディレクトリ作成');
	}

	/**
	 * action_edit_dir()
	 * ディレクトリの作成、移動、パージ、パーミッション設定
	 */
	public function action_edit_dir($id = null)
	{
		$model = '\Model_Flr_Dir' ;
		$is_create = false;

		if ($id)
		{
			$obj = $model::find($id, $model::authorized_option(array(), 'edit'));

			// not found
			if ( ! $obj)
			{
				$page = \Request::forge('sys/403')->execute();
				return new \Response($page, 403);
			}

			$prev_name = $obj->name;
			$prev_path = $obj->path;
			$title = $obj->name . '編集';
		}
		else
		{
			$obj = $model::forge();
			$title = 'ディレクトリ新規作成';
			$is_create = true;
		}
		$form = $model::form_definition('edit', $obj);

		// save
		if (\Input::post())
		{
			if (
				$obj->cascade_set(\Input::post(), $form, $repopulate = true) &&
				 \Security::check_token()
			)
			{

				$errors = array();
				$is_operation_done = true;
				$dirnname = \Input::post('name');
				$parent =  \Input::post('parent', LOCOMOUPLADPATH);

				// create Directory
				if(\Input::post('is_create'))
				{
					if (file_exists($parent.$dirnname))
					{
						$errors[] = 'そのディレクトリは既に存在します。';
						$is_operation_done = false;
					}
					elseif ( ! \File::create_dir($parent, \Input::post('name')))
					{
						$errors[] = 'ディレクトリの新規作成に失敗しました。';
						$is_operation_done = false;
					}
				}

				// edit Directory
				if( ! \Input::post('is_create'))
				{
					// rename
					if ($prev_name != $dirnname)
					{
						\File::rename($obj->path, dirname($obj->path).DS.$dirnname);
						$new_path = dirname($obj->path).DS.$dirnname;
					}
/*
ここから
リネームとムーブを一緒にする場合、リネーム内に入れるべきかちょっと考える。
*/
					// move
					if ($obj->path != $parent.$dirnname)
					{
						\File::copy_dir($obj->path, $parent.$dirnname);
						\File::delete_dir($obj->path, $recursive = true);
					}
				}


				// file operation done
				if ($is_operation_done)
				{
					// path
					$obj->path = rtrim($parent.$dirnname, '/').DS;

					//save
					if ($obj->save(null, true))
					{
							// success
							\Session::set_flash(
								'success',
								sprintf('%1$sの #%2$d を更新しました', self::$nicename, $obj->id)
							);
		
							// redirect
							$locomo_path = \Inflector::ctrl_to_dir(\Request::main()->controller.DS.\Request::main()->action);
							return \Response::redirect(\Uri::create($locomo_path.DS.$obj->id));
					}
					else
					{
						//save failed
						\Session::set_flash(
							'error',
							sprintf('%1$sの #%2$d を更新できませんでした', self::$nicename, $id)
						);
					}
				}
				// file operation failed
				else
				{
					array_unshift($errors, 'ファイルオペレーションに失敗しました。');
					$errors[] = 'どうしてもうまくいかないときにはパーミッションを確認してください。';
					//save failed
					\Session::set_flash('error', $errors);
				}
			}
			else
			{
				//edit view or validation failed of CSRF suspected
				if (\Input::method() == 'POST')
				{
					$errors = $form->error();
					// いつか、エラー番号を与えて詳細を説明する。そのときに二重送信でもこのエラーが出ることを忘れず言う。
					if ( ! \Security::check_token()) $errors[] = 'ワンタイムトークンが失効しています。送信し直してみてください。';
					\Session::set_flash('error', $errors);
				}
			}
		}

		


		$content = \View::forge('flr/edit');
		$content->set_global('is_create', $is_create);
		$content->set_global('form', $form, false);
		$this->template->content = $content;
		$this->template->set_global('title', 'ディレクトリ操作');
	}

	/**
	 * action_upload()
	 */
	public function action_upload($id = null)
	{
		$model = $this->model_name ;
		$obj = $model::forge();
		$form = $model::form_definition('edit', $obj);

// パーミッションをいじることができるのは、ディレクトリとファイル
// ディレクトリを編集しているときには、ディレクトリの新規作成、削除、パーミッションの変更ができる。ディレクトリの付け替えもできる？

// postがあるときのロジック
// postがあるときには、物理パスか$_FILESのいずれかが存在する
// 物理パスは、変更できるようにする。ディレクトリを選択できるように。

		$content = \View::forge('flr/edit');
		$content->set_global('form', $form, false);
		$this->template->content = $content;
		$this->template->set_global('title', 'ファイルアップロード');


	}

	/**
	 * check_auth()
	 */
	public static function check_auth($path)
	{
		// usergroups
		$usergroups = \Auth::get('usergroups');

		// always true
		if (in_array('-1', $usergroups) || in_array('-2', $usergroups) ) return true;

		$obj = \Model_Flr::find('first', array('where' => array(array('path', $path))));
		if ( ! $obj) return false;

		// check usergroups
		$is_allowed = false;
		foreach ($usergroups as $usergroup)
		{
			if (in_array($usergroup, $obj->permission_usergroup))
			{
				$is_allowed = true;
				break;
			}
		}
		if ($is_allowed) return true;

		// user_id
		$uid = \Auth::get('id');
		return in_array($uid, $obj->permission_user);
	}
}
