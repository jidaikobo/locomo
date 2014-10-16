<?php
namespace Kontiki_Core;
class Controller_Crud extends \Kontiki\Controller_Base
{

	// public $template = 'index_admin';
	public $template = 'index';

	/*
	 * todo
	 * 追加
	 * get で渡されると不味いもの
	 * config に whitelist がベストかも?
	 * memo -> fuel のプロパティは $_ で始める(特にモデル)
	 */
	protected $_get_black_list = array(
	);
	
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

		// $pagination とも兼ね合い
		$pagination_config = $this->pagination_config;
		$conditions['limit'] = \Input::get('limit') ?: $pagination_config['per_page'];
		$conditions['offset'] = \Input::get('offset') ?: \Pagination::get('offset');
		// 検索 $conditions に足していく
		// $pagination とも兼ね合い
		// 検索 $conditions に足していく
		// todo 現状and検索のみ
		// todo <, >, <=, >= の実装検討? core だからいらないか
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

		// var_dump($conditions); die();

		// 件数取得
		$count = $model::count($conditions);
		$view->set('hit', $count);

		// pagination
		$pagination_config['total_items'] = $count;
		// todo wl or bl の実装
		$get_query = \input::get();
		$pagination_config['pagination_url'] = \Uri::create('/'.$this->request->module.'/'.$action.'/', array(), $get_query);
		\Pagination::set_config($pagination_config);
		$view->set_safe('pagination', \Pagination::create_links());

		if ($deleted) {
			$view->set('items', $model::deleted('all', $conditions));
		} elseif($deleted == 'disabled') {
			$model::disable_filter();
			$view->set('items', $model::find('all', $conditions));
			$deleted = false;
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

		$conditions['where'][] = array('created_at', '>=', date('Y-m-d'));
		$conditions['where'][] = array('expired_at', '>=', date('Y-m-d'));

		return static::index_core($args);

	}

	/**
	 * action_index_expired()
	 */
	public function action_index_expired($pagenum = 1)
	{
		$model = $this->model_name;

		if (!isset($model::properties()['created_at'])) throw new \HttpNotFoundException;

		$conditions['where'][] = array('created_at', '<=', date('Y-m-d'));
		$conditions['where'][] = array('expired_at', '>=', date('Y-m-d'));

		return static::index_core($args);

	}

	public function action_index_revision() {
	}

	/*
	 * $id を取るが、表示形式がリストなので
	 * こちらに含めておく
	 */
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


	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible($pagenum = 1)
	{
		$model = $this->model_name;

		if (!isset($model::properties()['status'])) {
			throw new HttpNotFoundException;
		}

		$conditions['where'][] = array('status', '=', 'invisible');

		return static::index_core($args);
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

		if ($model instanceof \Orm\Model_Soft) {
			throw new HttpNotFoundException;
		}

		return static::index_core(array(), 'disabled');
	}




	/*
	 * ============================= 単票 data ココから =========================================
	 * $id を取る
	 * #元々はfind_item
	 */

	/**
	 * action_view()
	 */

	public function action_view($id = null)
	{
		$model = $this->model_name;

		if(! $obj = $model::find($id)) throw new \HttpNotFoundException;
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

	public function action_create() {
		$this->action_edit(null);
	}

	public function edit_core($id, $deleted) {
		$model = $this->model_name ;
		// todo
		$form = $model::form_definition('edit', $obj, $id);

		if ($model::properties()['workflow_status']) $conditions['where'][] = array('workflow_status', '!=', 'in_progress');
		// コントローラで、in_progressだったらstatusをinvisibleに

		/*
		 * todo 実験用
		 * ここから、Auth を observer に 含めない場合の実装
		 * どちらが、軽いか?
		 */
		// OR 検索
/*
		if (
			$model::properties()['status'] &&
			\Acl\Controller_Acl::auth($controller.'/view_invisible', $userinfo)
		) {
			$conditions['where'][] = array('workflow_status', '!=', 'in_progress');
		}
*/

	}

	public function action_edit($id = null) {
		$model = $this->model_name ;


		if (Input::post()) {
			var_dump(Input::post()); die();
		}

		if ($id) {
		} else {
		}

		$view = \View::forge('edit');

		//view
		$view->set_global('title', sprintf($this->titles['edit'], self::$nicename));
		$view->set_global('item', $obj, false);
		$view->set_global('form', $form, false);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_delete_deleted()
	 */
	public function action_delete_deleted($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect(\Uri::base());

		// 下記で見つからなければ、存在しないか
		// 削除済み項目
		if ($obj = $model::find_deleted($id)):
			// Cascading deleteの恩恵を受けられない

			//pre_delete_hook
			$obj = $this->pre_delete_hook($obj, 'delete');

			$obj->purge();
			// $model::delete_item($id);

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



	/* ==================== ココから実験用 ========================
	 * action_edit_xxx
	 * 恐らくこれが一番軽い
	 */
}

