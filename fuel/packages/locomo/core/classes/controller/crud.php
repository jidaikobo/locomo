<?php
namespace Locomo;
class Controller_Crud extends Controller_Base
{
	public $_index_template = 'index_admin';

	/**
	 * Presenterに渡り、ACLを確認するときに使う
	 * 個票を返すようなとき、その値を$_single_itemに渡すこと
	 */
	public $_single_item ;

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

	protected $titles = array(
		'deleted'   => '(削除済み項目)',
		'yet'       => '(予約項目)',
		'expired'   => '(期限切れ項目',
		'invisible' => '(の不可視項目)',
		'all'       => '(削除を含む全項目)',
	);


	/**
	 * action_index_admin()
	 * 管理者用の index
	 */
	public function action_index_admin()
	{
		$view = \View::forge($this->_index_template);

		// find & set pagination_config
		$view->set('items',  $this->paginated_find());

		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', \Pagination::get('total_items')); ///
		$view->set('is_deleted', false); ///
		$view->set_global('title', self::$nicename);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_index()
	 */
	public function action_index()
	{
		$view = \View::forge('index');

		// find & set pagination_config
		$view->set('items',  $this->paginated_find());

		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', \Pagination::get('total_items')); ///
		$view->set('is_deleted', false); ///
		$view->set_global('title', self::$nicename);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
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

		$view = \View::forge($this->_index_template);

		// find & set pagination_config
		$view->set('items',  $this->paginated_find($options, $model));

		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', \Pagination::get('total_items')); ///
		$view->set('is_deleted', false); ///
		$view->set_global('title', self::$nicename . 'の予約項目');

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
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

		$view = \View::forge($this->_index_template);

		// find & set pagination_config
		$view->set('items',  $this->paginated_find($options, $model));

		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', \Pagination::get('total_items')); ///
		$view->set('is_deleted', false); ///
		$view->set_global('title', self::$nicename . 'の期限切れ項目');

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible($pagenum = 1)
	{
		$model = $this->model_name;

		if (!isset($model::properties()['is_visible'])) throw new \HttpNotFoundException;

		$options['where'][] = array('is_visible', '=', 0);

		$view = \View::forge($this->_index_template);

		// find & set pagination_config
		$view->set('items',  $this->paginated_find($options, $model));

		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', \Pagination::get('total_items')); ///
		$view->set('is_deleted', false); ///
		$view->set_global('title', self::$nicename . 'の不可視項目');

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted()
	{
		$model = $this->model_name ;
		if ($model instanceof \Orm\Model_Soft) throw new \HttpNotFoundException;

		$view = \View::forge($this->_index_template);

		// find & set pagination_config
		$view->set('items',  $this->paginated_find(array(), null, true));

		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', \Pagination::get('total_items')); ///
		$view->set('is_deleted', false); ///
		$view->set_global('title', self::$nicename . 'の削除済み項目');

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/*
	 * action_index_all()
	 */
	public function action_index_all() {
		$model = $this->model_name ;

		if ($model instanceof \Orm\Model_Soft) throw new \HttpNotFoundException;

		$view = \View::forge($this->_index_template);

		// find & set pagination_config
		$view->set('items',  $this->paginated_find(array(), null, 'disabled'));

		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', \Pagination::get('total_items')); ///
		$view->set('is_deleted', false); ///
		$view->set_global('title', self::$nicename . 'の削除を含む全項目');

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
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
				sprintf('%1$s #%2$d は表示できません', self::$nicename, $id)
			);
			throw new \HttpNotFoundException;
			\Response::redirect($this->request->module);
		endif;

		//$_single_item
		$this->_single_item = $data['item'];

		//view
		$view = \View::forge('view');
		$view->set_global('item', $this->_single_item);
		$view->set_global('title', self::$nicename . '閲覧');

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	public function action_create() {
		return $this->action_edit(null);
	}

	public function action_edit($id = null) {
		
		$model = $this->model_name ;

		if ($id) {
			$obj = $model::find($id, $model::authorized_option(array(), 'edit'));
			if( ! $obj){
				$page = \Request::forge('content/403')->execute();
				return new \Response($page, 403);
			}
			$title = '#' . $id . ' ' . self::$nicename . '編集';
		} else {
			$obj = $model::forge();
			$title = self::$nicename . '新規作成';
		}
		$form = $model::form_definition('edit', $obj);

		/*
		 * save
		 */
		if (\Input::post()) :
			if (
				$obj->cascade_set(\Input::post(), $form, $repopulate = true) &&
				 \Security::check_token()
			):
				//save
				if ($obj->save(null, true)):
					//success
					\Session::set_flash(
						'success',
						sprintf('%1$sの #%2$d を更新しました', self::$nicename, $obj->id)
					);
					\Response::redirect(\Uri::create($this->request->module.'/edit/'.$obj->id));
				else:
					//save failed
					\Session::set_flash(
						'error',
						sprintf('%1$sの #%2$d を更新できませんでした', self::$nicename, $id)
					);
				endif;
			else:
				//edit view or validation failed of CSRF suspected
				if (\Input::method() == 'POST'):
					\Session::set_flash('error', $form->error());
				endif;
			endif;
		endif;

		//set _single_item
		$this->_single_item = $obj;

		//view
		$view = \View::forge('edit');
		$view->set_global('title', $title);
		$view->set_global('item', $this->_single_item, false);
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

			$obj->delete();

			\Session::set_flash(
				'success',
				sprintf('%1$sの #%2$d を削除しました', self::$nicename, $id)
			);
		else:
			\Session::set_flash(
				'error',
				sprintf('%1$sの #%2$d を削除できませんでした', self::$nicename, $id)
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
				sprintf('%1$s #%2$d は表示できません', self::$nicename, $id)
			);
			\Response::redirect(\Uri::base());
		}

		if ( ! $data['item'] = $model::find_deleted($id)):
			\Session::set_flash(
				'error',
				sprintf('%1$s #%2$d は表示できません', self::$nicename, $id)
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
				sprintf('%1$sの #%2$d を復活しました', self::$nicename, $id)
			);
		else:
			\Session::set_flash(
				'error',
				sprintf('%1$sの #%2$d を復活できませんでした', self::$nicename, $id)
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

			// 現状 Cascading deleteの恩恵を受けられない？ 要確認
			$obj->purge();

			\Session::set_flash(
				'success',
				sprintf('%1$sの #%2$d を完全に削除しました', self::$nicename, $id)
			);
		else:
			\Session::set_flash(
				'error',
				sprintf('%1$sの #%2$d を削除できませんでした', self::$nicename, $id)
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


	/*
	 * @param array    $options conditions for find
	 * @param str      $model model class name
	 * @param bool|str $deleted
	 * @param bool     $use_get_query use get query paramaters
	 * @param array    $pagination_config overwrite $this->pagination_config
	 *
	 * @return Model finded
	 */
	public function paginated_find($options = array(), $model = null, $deleted = false, $use_get_query = true, $pagination_config = null) {

		is_null($model) and $model = $this->model_name;
		$action = \Request::main()->action;

		if ($use_get_query) {
			$input_get = \Input::get();
		} else {
			$input_get = array();
		}
		if ($use_get_query and \Input::get()) {
			if (\Input::get('orders')) {
				$orders = array();
				foreach (\Input::get('orders') as $k => $v) {
					$orders[$k] = $v;
				}
				$options['order_by'] = $orders;
			}
			if (\Input::get('searches')) {
				foreach (\Input::get('searches') as $k => $v) {
					if ($v == false) continue;
					$options['where'][] = array($k, '=', $v);
				}
			}
			if (\Input::get('likes')) {
				$likes = array();
				foreach (\Input::get('likes') as $k => $v) {
					if ($v == false) continue;
					$options['where'][] = array($k, 'LIKE', '%' . $v . '%');
				}
			}
		}

		if ($deleted === 'disabled') {
			$model::disable_filter();
			$count = count($model::find('all', $options));
			$deleted = false;
		} elseif($deleted) {
			$count = count($model::find_deleted('all', $options));
		} else {
			$count = $model::count($options);
		}

		$pagination_config = $pagination_config ? array_merge($this->pagination_config, $pagination_config) : $this->pagination_config;

		$pagination_config['total_items'] = $count;
		$pagination_config['pagination_url'] = \Uri::create('/'.$this->request->module.'/'.$action.'/', array(), $input_get);

		if (isset($pagination_config['per_page'])) $options['limit'] = $pagination_config['per_page'];
		\Pagination::set_config($pagination_config);
		if (!isset($pagination_config['per_page'])) $options['limit'] = \Pagination::get('per_page');
		$options['offset'] = \Pagination::get('offset');

		if ($deleted === 'disabled') {
			$model::disable_filter();
			return $model::find('all', $options);
			$deleted = false;
		} elseif($deleted) {
			return $model::find_deleted('all', $options);
		} else {
			return $model::find('all', $options);
		}

	}


}
