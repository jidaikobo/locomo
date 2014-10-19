<?php
namespace Kontiki_Core;
class Controller_Crud extends \Kontiki\Controller_Base
{

	// public $template = 'index_admin';
	public $template = 'index';

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
		'index_all'       => '%1$s、削除を含む全項目',
		'view_deleted'    => '%1$s（削除済み）',
		'edit_deleted'    => '%1$s（削除済み）の編集',
		'confirm_delete'  => '%1$sを完全に削除してよろしいですか？',
		'revision'        => '%1$sの編集履歴',
	);

	/**
	 * @var array default setting of pagination
	 */
	protected $pagination_config = array(
		'uri_segment' => 3,
		'num_links' => 5,
		'per_page' => 20,
		'template' => array(
			'wrapper_start' => '<div class="pagination">',
			'wrapper_end' => '</div>',
			'active_start' => '<span class="current">',
			'active_end' => '</span>',
		),
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



	/*
	 * actions_index_***
	 * リスト表示
	 * # 元々は find_items で取っていた
	 */

	/**
	 * index_core()
	 * @param array    $conditions default conditions
	 * @param bool|str $deleted
	 *
	 * @return view response object
	 * @author shibata@jidaikobo.com and otegami@tsukitsume.com
	 */
	public function index_core($conditions = array(), $deleted = false)
	{

		$model = $this->model_name ;
		$action = \Request::main()->action;

		$view = \View::forge($this->template);

		$pagination_config = $this->pagination_config;
		$conditions['limit'] = \Input::get('limit') ?: $pagination_config['per_page'];
		$conditions['offset'] = \Input::get('offset') ?: \Pagination::get('offset');
		$pagination_config = $this->pagination_config;
		if (\Input::get()) {
			if (\Input::get('orders')) {
				$orders = array();
				foreach (\Input::get('orders') as $k => $v) {
					$orders[$k] = $v;
				}
				$conditions['order_by'] = array($orders);
			}
			if (\Input::get('searches')) {
				foreach (\Input::get('searches') as $k => $v) {
					$conditions['where'][] = array($k, '=', $v);
				}
			}
			if (\Input::get('likes')) {
				$likes = array();
				foreach (\Input::get('likes') as $k => $v) {
					$conditions['where'][] = array($k, 'LIKE', '%' . $v . '%');
				}
			}
		}

		// 件数取得
		$count = $model::count($conditions);
		$view->set('hit', $count);

		// pagination
		$pagination_config['total_items'] = $count;
		$pagination_config['pagination_url'] = \Uri::create('/'.$this->request->module.'/'.$action.'/', array(), \Input::get());
		\Pagination::set_config($pagination_config);
		$view->set_safe('pagination', \Pagination::create_links());

		if ($deleted === 'disabled') {
			$model::disable_filter();
			$view->set('items', $model::find('all', $conditions));
			$deleted = false;
		} elseif($deleted) {
			$view->set('items', $model::find_deleted('all', $conditions));
		} else {
			$view->set('items', $model::find('all', $conditions));
		}

		//view
		$view->set('is_deleted', $deleted);
		$view->set_global('title', sprintf($this->titles[$action], self::$nicename));

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_index_admin()
	 * 管理者用の index
	 */
	public function action_index_admin()
	{
		$this->template = 'index_admin';

		return static::index_core();
	}

	/**
	 * action_index()
	 */
	public function action_index()
	{
		return static::index_core();
	}

	/**
	 * action_index_yet()
	 * 予約項目
	 * created_at が未来のもの
	 */
	public function action_index_yet()
	{
		$model = $this->model_name;

		if (!isset($model::properties()['created_at'])) throw new HttpNotFoundException;

		$options['where'][] = array('created_at', '>=', date('Y-m-d'));
		$options['where'][] = array('expired_at', '>=', date('Y-m-d'));

		return static::index_core($options);

	}

	/**
	 * action_index_expired()
	 */
	public function action_index_expired($pagenum = 1)
	{
		$model = $this->model_name;

		if (!isset($model::properties()['created_at'])) throw new \HttpNotFoundException;

		$options['where'][] = array('created_at', '<=', date('Y-m-d'));
		$options['where'][] = array('expired_at', '>=', date('Y-m-d'));

		return static::index_core($options);

	}

	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible($pagenum = 1)
	{
		$model = $this->model_name;

		if (!isset($model::properties()['status'])) {
			throw new HttpNotFoundException;
		}

		$options['where'][] = array('status', '=', 'invisible');

		return static::index_core($options);
	}


	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted()
	{
		$model = $this->model_name ;

		if ($model instanceof \Orm\Model_Soft) {
			throw new HttpNotFoundException;
		}

		return static::index_core(array(), true);
	}

	/*
	 * action_index_all()
	 */
	public function action_index_all() {
		$model = $this->model_name ;

		$this->template = 'index_admin';
		if ($model instanceof \Orm\Model_Soft) {
			throw new HttpNotFoundException;
		}

		return static::index_core(array(), 'disabled');
		
		$obj = static::index_core(array(), 'disabled');
		// $obj = $this->add_status_all($obj);
		return $obj;
	}

	/*
	 * index_add 用にステータスを付与する
	 * 後、下記と統合する
	 */
	private function add_status_all($objects = array()) {
		foreach ($objects as $obj) {
			static::add_status($obj);
		}
		return $objects;
	}

	/*
	 * @param \Orm\Model
	 */
	private function add_status($obj = null) {
		if (!$obj->status) {
			if (isset($obj::properties()['created_at'])) {
				if (strtotime($obj->created_at) > time()) $status = 'yet';
				if (isset($obj::properties()['expired_at'])) {
					if (strtotime($obj->expired_at) < time()) $status = 'expired';
					/*if (strtotime($obj->expired_at) > time())*/ $status = 'yet';
				}
			}
			// var_dump($obj->status); die();
		}

		array(
			'invisible', // 不過視 in_progress
			'revision', // リビジョン
			'expired', // 期限切れ
			'yet', // 予約済み
			'deleted', // 削除済み
		);
	}

	/**
	 * action_view()
	 */

	public function action_view($id = null)
	{
		$model = $this->model_name;

		is_null($id) and \Response::redirect(\Uri::base());

		$authorized_option = $model::authorized_option();

		if ( ! $data['item'] = $model::find($id, $authorized_option)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], self::$nicename, $id)
			);
			throw new \HttpNotFoundException;
			\Response::redirect($this->request->module);
		endif;

		//view
		$view = \View::forge('view');
		$view->set_global('item', $data['item']);
		$view->set_global('title', sprintf($this->titles['view'], self::$nicename));

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}


	public function action_create() {
		return $this->action_edit(null);
	}

	public function modify_cascaded($object = null) {
		$object;
	}

	public function action_edit($id = null) {
		$model = $this->model_name ;

		if ($id) {
			$obj = $model::find($id, $model::authorized_option());
			if( ! $obj){
				$page = \Request::forge('content/403')->execute();
				return new \Response($page, 403);
			}
			$title = sprintf($this->titles['edit'], $this->request->module);
		} else {
			$obj = $model::forge();
			$title = sprintf($this->titles['create'], $this->request->module);
		}

		$form = $model::form_definition('edit', $obj, $id);

		/*
		 * save
		 */
		if (\Input::post()) :
			if (
				$obj->cascade_set(\Input::post(), $form, $repopulate = true) &&
				 \Security::check_token()
			):
/*
				//pre_save_hook
				$obj = $this->pre_save_hook($obj, 'edit');
*/
				//save
				if ($obj->save()):
/*
				//post_save_hook
				$obj = $this->post_save_hook($obj, 'edit');
*/
				//message
				\Session::set_flash(
					'success',
					sprintf($this->messages['edit_success'], self::$nicename, $obj->id)
				);
				\Response::redirect(\Uri::create($this->request->module.'/edit/'.$obj->id));
			else:
				\Session::set_flash(
					'error',
					sprintf($this->messages['edit_error'], self::$nicename, $id)
				);
			endif;

			//edit view or validation failed of CSRF suspected
			else:
				if (\Input::method() == 'POST'):
					\Session::set_flash('error', $form->error());
				endif;
			endif;
		endif;

		$view = \View::forge('edit');

		$view->set_global('title', $title);
		$view->set_global('item', $obj, false);
		$view->set_global('form', $form, false);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_delete()
	 */
	public function action_delete($id = null)
	{
		$model = $this->model_name ;

		is_null($id) and \Response::redirect(\Uri::base());

		if ($obj = $model::find($id)):
			//pre_delete_hook
			$obj = $this->pre_delete_hook($obj, 'delete');

			$obj->delete();

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
	 * action_confirm_delete()
	 */
	public function action_confirm_delete($id = null)
	{
		$model = $this->model_name ; 
		if (!$id) {
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], self::$nicename, $id)
			);
			\Response::redirect(\Uri::base());
		}

		if ( ! $data['item'] = $model::find_deleted($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], self::$nicename, $id)
			);
			echo 'purge'; die();
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

		if ($obj = $model::find_deleted($id)):
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

		if ($obj = $model::find_deleted($id)):

			//pre_delete_hook
			$obj = $this->pre_delete_hook($obj, 'delete');

			// 現状 Cascading deleteの恩恵を受けられない
			$obj->purge();

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

	// リビジョン
	/*
	public function action_index_revision() {
	}
	 */

	/*
	public function action_revision_list($id = null)
	{
		$model = $this->model_name;

		if (!isset($model::properties()['status'])) {
			throw new HttpNotFoundException;
		}

		// todo primary_key が 2 つ以上
		$pk = $model::primary_key();
		$conditions['where'][] = array('status', '=', 'revision');
		$conditions['where'][] = array($pk[0], '=', $id);

		return static::index_core($args);
	}
	 */

}


