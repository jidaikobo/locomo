<?php
namespace Revision;
trait Controller_Revision
{
	/**
	 * revision_modify_data()
	 */
	public function revision_modify_data($obj, $mode = null)
	{
		return $obj;
	}

	/**
	 * revision_save_hook()
	 */
	public function revision_save_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);

		//actionsetでrevisionが有効だったらrevisionを追加する
		if( ! array_key_exists ('view_revision' , self::$actionset)):
			return $obj;
		else:
			$primary_key = $obj::get_primary_key();
			$args = array();
			$args['controller']    = $this->request->module;
			$args['controller_id'] = $obj->$primary_key[0];
			$args['data']          = serialize($this->revision_modify_data($obj, 'insert_revision'));
			$args['comment']       = \Input::post('revision_comment') ?: '';
			$args['created_at']    = date('Y-m-d H:i:s');
			$args['modifier_id']   = isset($obj->modifier_id) ? $obj->modifier_id : 0;
			$model = \Revision\Model_Revision::forge($args);
			$model->insert_revision();
		endif;

		return $obj;
	}

	/**
	 * action_index_revision()
	 */
	public function action_index_revision($id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());
		$model = \Revision\Model_Revision::forge();

		if ( ! $revisions = $model::find_revisions($this->request->module, $id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['revision_error'], self::$nicename, $id)
			);
			return \Response::redirect(\Uri::create($this->request->module.'/index_admin'));
		endif;

		//view
		$view = \View::forge(\Locomo\Util::fetch_tpl('/revision/views/index.php'));

		$view->set_global('items', $revisions);
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

		//unserialize
		$data                = unserialize($revisions->data);
		$data->controller    = $revisions->controller;
		$data->controller_id = $revisions->controller_id;
		$data->comment       = $revisions->comment;

		//view
		$view = \View::forge('edit');
		$view->set_global('item', $data);
		$view->set_global('title', sprintf($this->titles['revision'], self::$nicename));
		$view->set_global('is_revision', true);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

}