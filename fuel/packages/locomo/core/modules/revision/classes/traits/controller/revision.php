<?php
namespace Revision;
trait Traits_Controller_Revision
{
	/**
	 * action_index_revision()
	 */
	public function action_index_revision($model_simple_name, $page = 1)
	{
		//model
		$model = '\\'.ucfirst($this->request->module).'\\Model_'.ucfirst($model_simple_name);

		//option - ise \Module\Model_Module::$_option_options['range']
		$opt = false;
		if(\Input::get('opt')):
			if( ! isset($model::$_option_options[\Input::get('opt')])) die('missing $_option_options.');
			$opt = $model::$_option_options[\Input::get('opt')] ;
		endif;

		//view
		$view = \View::forge(PKGCOREPATH.'modules/revision/views/index_revision.php');
		$view = \Revision\Model_Revision::find_all_revisions($view, $model, $opt);

		if( ! $view):
			\Session::set_flash('error', '表示できませんでした');
			return \Response::redirect(\Uri::base());
		endif;

		//assign
		if($opt):
			$view->set_global('title', $opt['nicename'].'履歴');
		else:
			$view->set_global('title', '履歴');
		endif;
		$view->set_global('controller', 'user');
		$view->set_global('subject', $model::get_default_field_name('subject'));
		$view->set_global('model_simple_name', $model_simple_name);
		$view->set_global('opt', \Input::get('opt') ? '?opt='.\Input::get('opt') : '');

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_each_index_revision()
	 */
	public function action_each_index_revision($model_simple_name, $id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		//model
		$model = '\\'.ucfirst($this->request->module).'\\Model_'.ucfirst($model_simple_name);

		//履歴を取得
		if ( ! $revisions = \Revision\Model_Revision::find_revisions($model, $id)):
			\Session::set_flash('error', '履歴を取得できませんでした');
			return \Response::redirect(\Uri::create($this->request->module.'/view/'.$id));
		endif;

		//add_actionset
		$action = array(
			'url' => $this->request->module.'/edit/'.$id,
			'menu_str' => '編集画面に戻る',
		);
		\Actionset::add_actionset($this->request->module, 'ctrl', 'back', $action);

		//view
		$view = \View::forge(\Util::fetch_tpl('/revision/views/each_index_revision.php'));
		$view->set_global('items', $revisions);
		$view->set_global('controller', $this->request->module);
		$view->set_global('title', '履歴一覧');
		$view->set_global('subject', $model::get_default_field_name('subject'));
		$view->set_global('model_simple_name', $model_simple_name);
		$view->set_global('opt', \Input::get('opt') ? '?opt='.\Input::get('opt') : '');

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_view_revision()
	 */
	public function action_view_revision($model_simple_name, $id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());
		$model = \Revision\Model_Revision::forge();

		if ( ! $revisions = $model::find_revision($id)):
			\Session::set_flash('error', '履歴を取得できませんでした');
			return \Response::redirect(\Uri::base());
		endif;

		//add_actionset
		$action = array(
			'url' => $this->request->module.'/each_index_revision/'.$revisions->pk_id,
			'menu_str' => '履歴一覧に戻る',
		);
		\Actionset::add_actionset($this->request->module, 'ctrl', 'back', $action);
		$action = array(
			'url' => $this->request->module.'/edit/'.$revisions->pk_id,
			'menu_str' => '編集画面に戻る',
		);
		\Actionset::add_actionset($this->request->module, 'ctrl', 'back2', $action);

		//unserialize
		$data          = unserialize($revisions->data);

		//model
		$original_model = '\\'.ucfirst($this->request->module).'\\Model_'.ucfirst($model_simple_name);
		$pk = $original_model::get_primary_keys('first');

		//option - ise \Module\Model_Module::$_option_options['range']
		$opt = false;
		if(\Input::get('opt')):
			if( ! isset($original_model::$_option_options[\Input::get('opt')])) die('missing $_option_options.');
			$opt = $original_model::$_option_options[\Input::get('opt')] ;
		endif;


		//form definition
		$data->$pk   = $revisions->pk_id;
		$template = 'edit';
		if(isset($opt['form_definition'])):
			$form = $original_model::{$opt['form_definition']}('revision', $data);
		else:
			//普通のform_definition
			$form = $original_model::form_definition('revision', $data);
		endif;

		//template
		if(isset($opt['template']) && ! empty($opt['template'])):
			//指定テンプレート
			$template = $opt['template'];
		elseif(isset($opt['template']) && empty($opt['template'])):
			//bulk
			$template = PKGCOREPATH.'modules/bulk/views/bulk.php';
		endif;
		
		//view
		$view = \View::forge($template);
		$view->set_global('form', $form, false);
		$view->set_global('item', $data);
		$view->set_global('title', '履歴');
		$view->set_global('is_revision', true);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

}