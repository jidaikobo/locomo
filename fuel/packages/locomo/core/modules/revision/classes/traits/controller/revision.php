<?php
namespace Revision;
trait Traits_Controller_Revision
{
	/**
	 * revision_modify_data()
	 */
	public function revision_modify_data($obj, $mode = null)
	{
		return $obj;
	}

	/**
	 * action_index_revision()
	 */
	public function action_index_revision($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		//まずオリジナルの項目を取得する
		$original_model = $this->model_name;
		$authorized_option = $original_model::authorized_option();
		$original = $original_model::find($id, $authorized_option);
		$model_class = $original ? get_class($original) : null;

		//履歴を取得
		$model = \Revision\Model_Revision::forge();
		if ( ! $revisions = $model::find_revisions($model_class, $id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['revision_error'], self::$nicename, $id)
			);
			return \Response::redirect(\Uri::create($this->request->module.'/view/'.$id));
		endif;

		//add_actionset
		$action = array(
			'url' => $this->request->module.'/edit/'.$id,
			'menu_str' => '編集画面に戻る',
		);
		\Actionset::add_actionset($this->request->module, 'ctrl', 'back', $action);

		//view
		$view = \View::forge(\Util::fetch_tpl('/revision/views/index.php'));
		$view->set_global('items', $revisions);
		$view->set_global('controller', $this->request->module);
		$view->set_global('title', sprintf($this->titles['revision'], self::$nicename));

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_view_revision()
	 */
	public function action_view_revision($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());
		$model = \Revision\Model_Revision::forge();

		if ( ! $revisions = $model::find_revision($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['revision_error'], self::$nicename, $id)
			);
			return \Response::redirect(\Uri::create($this->request->module.'/index_admin'));
		endif;

		//add_actionset
		$action = array(
			'url' => $this->request->module.'/index_revision/'.$revisions->pk_id,
			'menu_str' => '履歴一覧に戻る',
		);
		\Actionset::add_actionset($this->request->module, 'ctrl', 'back', $action);
		$action = array(
			'url' => $this->request->module.'/edit/'.$revisions->pk_id,
			'menu_str' => '編集画面に戻る',
		);
		\Actionset::add_actionset($this->request->module, 'ctrl', 'back2', $action);

		//unserialize
		$data                = unserialize($revisions->data);
		$data->controller    = $this->request->module;
		$data->controller_id = $revisions->pk_id;
		$data->comment       = $revisions->comment;

		//form definition
		$original_model = $this->model_name;
		$form = $original_model::form_definition('revision', $data, $revisions->pk_id);

		//view
		$view = \View::forge('edit');
		$view->set_global('form', $form, false);
		$view->set_global('item', $data);
		$view->set_global('title', sprintf($this->titles['revision'], self::$nicename));
		$view->set_global('is_revision', true);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

}