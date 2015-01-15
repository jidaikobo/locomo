<?php
namespace Locomo;
class Controller_Flr extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Bulk;

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
	 * action_index()
	 * user module is not for public.
	 */
	public function action_index()
	{
		return \Response::redirect(\Uri::create('usr/index_admin'));
	}

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
	 * action_edit_dir()
	 */
	public function action_edit_dir()
	{
		$model = $this->model_name ;
		$obj = $model::forge();
		$form = $model::form_definition_edit_dir('edit', $obj);

		$content = \View::forge('flr/edit');
		$content->set_global('form', $form, false);
		$this->template->content = $content;
		$this->template->set_global('title', 'ファイルアップロード');
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
	 * user_auth_find()
	 */
	public static function user_auth_find()
	{
		// honesty at this case, ($pkid == \Auth::get('id')) is make sence.
		// this is a sort of sample code.
		$pkid = \Request::main()->id;
		$obj = \Model_Usr::find($pkid);

		// add allowed to show links at actionset
		\Auth::instance()->add_allowed(array(
			'\\Controller_Usr/edit',
			'\\Controller_Usr/view',
		));

		return ($obj->id == \Auth::get('id'));
	}
}
