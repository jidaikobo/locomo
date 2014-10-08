<?php
namespace Kontiki_Core;
class Controller_Crud extends \Kontiki\Controller_Base
{
	/**
	 * @var array default languages of flash messages
	 */
	protected $messages = array(
		'auth_error'       => '権限がありません',
		'view_error'       => '%1$s #%2$d は表示できません',
		'create_success'   => '%1$sに #%2$d を新規作成しました',
		'create_error'     => '%1$sに保存できませんでした',
		'edit_success'     => '%1$sの #%2$d を更新しました',
		'edit_error'       => '%1$sの #%2$d を更新できませんでした',
		'delete_success'   => '%1$sの #%2$d を削除しました',
		'delete_error'     => '%1$sの #%2$d を削除できませんでした',
		'undelete_success' => '%1$sの #%2$d を復活しました',
		'undelete_error'   => '%1$sの #%2$d を復活できませんでした',
		'purge_success'    => '%1$sの #%2$d を完全に削除しました',
		'purge_error'      => '%1$sの #%2$d を削除できませんでした',
		'revision_error'   => '%1$sの #%2$d の編集履歴を取得できませんでした',
	);

	/**
	 * @var array default languages of page title
	 */
	protected $titles = array(
		'index_admin'     => '%1$s',
		'index'           => '%1$s',
		'view'            => '%1$s',
		'create'          => '%1$sの新規作成',
		'edit'            => '%1$sの編集',
		'index_deleted'   => '%1$sの削除済み項目',
		'index_yet'       => '%1$sの予約項目',
		'index_expired'   => '%1$sの期限切れ項目',
		'index_invisible' => '%1$sの不可視項目',
		'view_deleted'    => '%1$s（削除済み）',
		'edit_deleted'    => '%1$s（削除済み）の編集',
		'confirm_delete'  => '%1$sを完全に削除してよろしいですか？',
		'revision'        => '%1$sの編集履歴',
	);

	/**
	 * @var array default setting of pagination
	 */
	protected $pagination_params = array(
		'uri_segment' => 0,
		'num_links'   => 5,
		'per_page'    => 20,
	);

	/**
	 * pre_save_hook()
	 */
	public function pre_save_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);
		return $obj;
	}

	/**
	 * post_save_hook()
	 */
	public function post_save_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);
		return $obj;
	}

	/**
	 * pre_delete_hook()
	 */
	public function pre_delete_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);
		return $obj;
	}

	/**
	 * post_delete_hook()
	 */
	public function post_delete_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);
		return $obj;
	}

	/**
	 * index_core()
	 * @param array $args
	 * @param str   $args[mode] [deleted|yet|expired|reserved]
	 * @param str   $args[action] [index|index_deleted]
	 * @param str   $args[template] [index]
	 * @param int   $args[pagenum] user requested value
	 *
	 * @return view response object
	 * @author shibata@jidaikobo.com
	 */
	public function index_core($args = array())
	{
		//args
		$mode     = @$args['mode']     ?: '' ;
		$action   = @$args['action']   ?: 'index' ;
		$template = @$args['template'] ?: 'index' ;
		$pagenum  = @$args['pagenum']  ?: 0 ;
		$pagenum  = \Input::get('offset') ?: $pagenum ;
		$per_page = \Input::get('limit')  ?: $this->pagination_params['per_page'] ;
		$offset   = $pagenum == 1 ? 0 : $pagenum * $per_page - $per_page;

		//get results
		$model = $this->model_name ;
		$args = array(
			'limit'  => $per_page,
			'offset' => $offset,
			'mode'   => $mode
		);
		$items = $model::find_items($args);

		//pagination
		$pagination_config = array(
			'pagination_url' => \Uri::create('/'.$this->request->module.'/'.$action.'/', array(), \input::get()),
			'uri_segment'    => 3,
			'num_links'      => $this->pagination_params['num_links'],
			'per_page'       => $per_page,
			'total_items'    => $items->count,
		);
		$pagination = \Pagination::forge($this->request->module, $pagination_config);

		//view
		$view = \View::forge($template);
		$view->set('hit', $items->count);
		$view->set('items', $items->results);
		$view->set('is_deleted', $mode == 'deleted' ? true : false);
		$view->set('pagination', $pagination->render(), false);
		$view->set_global('title', sprintf($this->titles[$action], self::$nicename));

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin($pagenum = 1)
	{
		$args = array(
			'pagenum'  => $pagenum,
			'template' => 'index_admin',
		);
		return self::index_core($args);
	}

	/**
	 * action_index()
	 */
	public function action_index($pagenum = 1)
	{
		$args = array(
			'pagenum' => $pagenum,
		);
		return self::index_core($args);
	}

	/**
	 * action_view()
	 */
	public function action_view($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		if ( ! $data['item'] = $model::find_item($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], self::$nicename, $id)
			);
			\Response::redirect($this->request->module);
		endif;

		//view
		$view = \View::forge('view');
		$view->set_global('item', $data['item']);
		$view->set_global('title', sprintf($this->titles['view'], self::$nicename));

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_create()
	 */
	public function action_create()
	{
		$model = $this->model_name ;
		$form = $model::form_definition('edit');

		if (\Input::method() == 'POST'):
			if ($form->validation()->run() && \Security::check_token()):
				$args = array();
				foreach(\Input::post() as $field => $value):
					if( ! \DBUtil::field_exists($model::get_table_name(), array($field))) continue;
					$args[$field] = $value;
				endforeach;

				$obj = $model::forge($args);

				//pre_save_hook
				$obj = $this->pre_save_hook($obj, 'create');

				if ($obj and $obj->save()):

					//post_save_hook
					$obj = $this->post_save_hook($obj, 'create');

					//save relations
//					$obj = $model::insert_relations($obj->id);
					
					\Session::set_flash(
						'success',
						sprintf($this->messages['create_success'], self::$nicename, $obj->id)
					);
					\Response::redirect(\Uri::create($this->request->module.'/edit/'.$obj->id));
				else:
					\Session::set_flash(
						'error',
						sprintf($this->messages['create_error'], self::$nicename)
					);
				endif;
			else:
				$form->repopulate();
				\Session::set_flash('error', $form->error());
			endif;
		endif;

		//view
		$view = \View::forge('create');
		$view->set_global('title', sprintf($this->titles['create'], self::$nicename));
		$view->set_global('form', $form, false);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * edit_core()
	 */
	public function edit_core($id = null, $obj = null, $redirect = null, $title = null)
	{
		if($id == null || $obj == null || $redirect == null) \Response::redirect($this->request->module);

		$model = $this->model_name ;
		$form = $model::form_definition('edit', $obj, $id);
		$view = \View::forge('edit');

		//validation succeed
		if ($form->validation()->run() && \Security::check_token()):

			//prepare self fields
			foreach(\Input::post() as $field => $value):
				if( ! \DBUtil::field_exists($model::get_table_name(), array($field))) continue;
				$obj->$field = $value;
			endforeach;

			//pre_save_hook
			$obj = $this->pre_save_hook($obj, 'edit');

			//save
			if ($obj->save()):

				//post_save_hook
				$obj = $this->post_save_hook($obj, 'edit');

				//save relations
//				$model::delete_relations($obj->id);
//				$obj = $model::insert_relations($obj->id);

				//message
				\Session::set_flash(
					'success',
					sprintf($this->messages['edit_success'], self::$nicename, $id)
				);
				\Response::redirect($redirect);
			else:
				\Session::set_flash(
					'error',
					sprintf($this->messages['edit_error'], self::$nicename, $id)
				);
			endif;
		//edit view or validation failed of CSRF suspected
		else:
			if (\Input::method() == 'POST'):
				$form->repopulate();
				\Session::set_flash('error', $form->error());
			endif;

//			$view->set_global('item', $obj, false);
		endif;

		//view
		$view->set_global('title', sprintf($this->titles['edit'], self::$nicename));
		$view->set_global('item', $obj, false);
		$view->set_global('form', $form, false);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_edit()
	 */
	public function action_edit($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		if ( ! $obj = $model::find_item($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], self::$nicename, $id)
			);
			\Response::redirect($this->request->module);
		endif;
		$title = sprintf($this->titles['edit'], $this->request->module);

		//edit core
		return $this->edit_core($id, $obj, $this->request->module.'/edit/'.$id, $title);
	}

	/**
	 * action_delete()
	 */
	public function action_delete($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		if($obj = $model::find_item($id)):
			//pre_delete_hook
			$obj = $this->pre_delete_hook($obj, 'delete');

			$model::delete_item($obj);

			//pre_delete_hook
			$obj = $this->pre_delete_hook($obj, 'delete');

			\Session::set_flash(
				'success',
				sprintf($this->messages['delete_success'], self::$nicename, $id)
			);
		else:
			\Session::set_flash(
				'error',
				sprintf($this->messages['delete_error'], self::$nicename, $id)
			);
		endif;

		return \Response::redirect(\Uri::create($this->request->module.'/index_deleted'));
	}

	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted($pagenum = 1)
	{
		$args = array(
			'pagenum'  => $pagenum,
			'action'   => 'index_deleted',
			'template' => 'index_admin',
			'mode'     => 'deleted',
		);
		return self::index_core($args);
	}

	/**
	 * action_index_yet()
	 */
	public function action_index_yet($pagenum = 1)
	{
		$args = array(
			'pagenum'  => $pagenum,
			'action'   => 'index_yet',
			'template' => 'index_admin',
			'mode'     => 'yet',
		);
		return self::index_core($args);
	}

	/**
	 * action_index_expired()
	 */
	public function action_index_expired($pagenum = 1)
	{
		$args = array(
			'pagenum'  => $pagenum,
			'action'   => 'index_expired',
			'template' => 'index_admin',
			'mode'     => 'expired',
		);
		return self::index_core($args);
	}

	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible($pagenum = 1)
	{
		$args = array(
			'pagenum'  => $pagenum,
			'action'   => 'index_invisible',
			'template' => 'index_admin',
			'mode'     => 'invisible',
		);
		return self::index_core($args);
	}

	/**
	 * action_confirm_delete()
	 */
	public function action_confirm_delete($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		if ( ! $data['item'] = $model::find_item($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], self::$nicename, $id)
			);
			\Response::redirect(\Uri::create($this->request->module.'/index_deleted'));
		endif;

		//view
		$view = \View::forge('view');
		$view->set('item', $data['item']);
		$view->set('is_delete_deleted', true);
		$view->set_global('title', sprintf($this->titles['confirm_delete'], self::$nicename));

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_undelete()
	 */
	public function action_undelete($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		if ($obj = $model::find_item($id)):
			$obj->undelete();

			\Session::set_flash(
				'success',
				sprintf($this->messages['undelete_success'], self::$nicename, $id)
			);
		else:
			\Session::set_flash(
				'error',
				sprintf($this->messages['undelete_error'], self::$nicename, $id)
			);
		endif;

		return \Response::redirect(\Uri::create($this->request->module.'/index_admin'));
	}

	/**
	 * action_delete_deleted()
	 */
	public function action_delete_deleted($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		if ($obj = $model::find_item($id)):
			// Cascading deleteの恩恵を受けられない
			$obj->purge();

			//pre_delete_hook
			$obj = $this->pre_delete_hook($obj, 'delete');

			$model::delete_item($id);

			//pre_delete_hook
			$obj = $this->pre_delete_hook($obj, 'delete');

			\Session::set_flash(
				'success',
				sprintf($this->messages['purge_success'], self::$nicename, $id)
			);
		else:
			\Session::set_flash(
				'error',
				sprintf($this->messages['purge_error'], self::$nicename, $id)
			);
		endif;

		return \Response::redirect(\Uri::create($this->request->module.'/index_deleted'));
	}

	/**
	 * get_items()
	 */
	public function get_items()
	{
		$model = $this->model_name ;
		$arg = array(
			'limit'  => \Input::get('limit') ?: 10,
			'offset' => \Input::get('offset') ?: 0,
		);
		//view
		$this->response($model::find_items($arg)->results);
	}

	/**
	 * get_item()
	 */
	public function get_item()
	{
		if( ! ($id = intval( \Input::get('id')))) die();
		//view
		$model = $this->model_name ;
		$this->response($model::find_item($id));
	}
}
