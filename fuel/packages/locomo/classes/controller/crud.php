<?php
namespace Locomo;
class Controller_Crud extends Controller_Base
{
	// public $_master_template = 'default';
	public $_template = 'default';
	public $_content_template = null;

	/**
	 * @var array default setting of pagination
	 */
	protected $pagination_config = array(
		'uri_segment' => 3,
		'num_links'   => 5,
		'per_page'    => 20,
		'template' => array(
			'wrapper_start' => '<div class="pagination">',
			'wrapper_end'   => '</div>',
			'active_start'  => '<span class="current">',
			'active_end'    => '</span>',
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
	 * @param $options
	 * @param $model
	 * @param $deleted
	 */
	public function action_index_admin()
	{
		$model = $this->model_name;
		$contetnt = \View::forge($this->_content_template ?: 'index_admin');

		//$model::paginated_find_use_get_query(false);
		$contetnt->set('items',  $model::paginated_find(array(), $this->pagination_config));

		$contetnt->base_assign();
		$contetnt->set_global('title', static::$nicename);
		$this->template->content = $contetnt;
	}

	/**
	 * action_index()
	 */
	public function action_index()
	{
		$contetnt = \View::forge($this->_content_template ?: 'index');
		static::action_index_admin();//$options, $model, $deleted);
	}

	/**
	 * action_index_yet()
	 * 予約項目
	 * created_at が未来のもの
	 */
	public function action_index_yet()
	{
		$model = $this->model_name;

		if (!isset($model::properties()['created_at']) or !isset($model::properties()['expired_at'])) throw new \HttpNotFoundException;

		$model::$_conditions['where'][] = array('created_at', '>=', date('Y-m-d'));
		$model::$_conditions['where'][] = array('expired_at', '>=', date('Y-m-d'));

		\View::set_global('title', static::$nicename . '予約項目');
		static::action_index_admin();
	}

	/**
	 * action_index_expired()
	 */
	public function action_index_expired()
	{
		$model = $this->model_name;
		if (!isset($model::properties()['created_at']) or !isset($model::properties()['expired_at'])) throw new \HttpNotFoundException;

		$model::$_conditions['where'][] = array('created_at', '<=', date('Y-m-d'));
		$model::$_conditions['where'][] = array('expired_at', '<=', date('Y-m-d'));

		\View::set_global('title', static::$nicename . 'の期限切れ項目');
		static::action_index_admin();
	}

	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible()
	{
		$model = $this->model_name;
		if (!isset($model::properties()['is_visible'])) throw new \HttpNotFoundException;

		$model::$_conditions['where'][] = array('is_visible', '=', 0);

		\View::set_global('title', static::$nicename . 'の不可視項目');
		static::action_index_admin();
	}

	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted()
	{
		$model = $this->model_name;
		if ($model instanceof \Orm\Model_Soft) throw new \HttpNotFoundException;

		$deleted_column = $model::soft_delete_property('deleted_field', 'deletd_at');
		$model::$_conditions['where'][] = array($deleted_column, 'IS NOT', null);

		$model::disable_filter();
		//static::enable_filter();

		\View::set_global('title', static::$nicename . 'の削除済み項目');
		static::action_index_admin();
	}

	/*
	 * action_index_all()
	 */
	public function action_index_all()
	{
		$model = $this->model_name;
		if ($model instanceof \Orm\Model_Soft) throw new \HttpNotFoundException;

		$model::disable_filter();
		\View::set_global('title', static::$nicename . 'の削除を含む全項目');
		static::action_index_admin();
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

		$contetnt = \View::forge($this->_content_template ?: 'view');
		$model = $this->model_name;

		is_null($id) and \Response::redirect(\Uri::base());

		$authorized_option = $model::authorized_option();

		if ( ! $item = $model::find($id, $authorized_option)):
			\Session::set_flash(
				'error',
				sprintf('%1$s #%2$d は表示できません', self::$nicename, $id)
			);
			throw new \HttpNotFoundException;
			\Response::redirect($this->request->module);
		endif;

		$item = $model::plain_definition('view', $item)->build_plain();
		//view
		$contetnt = \View::forge('view');
		$contetnt->set_global('item', $item);
		$contetnt->set_global('title', self::$nicename . '閲覧');
		$this->template->content = $contetnt;
		\Auth_Acl_Locomoacl::set_item($this);
		$view->base_assign($item);
	}

	public function action_create() {
		static::action_edit(null);
	}

	public function action_edit($id = null)
	{
		$model = $this->model_name ;
		$content = \View::forge($this->_content_template ?: 'edit');

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

		//add_actionset
		$ctrl_url = \Inflector::ctrl_to_dir($this->request->controller);
		$action['urls'][] = \Html::anchor($this->request->module.DS.$ctrl_url.DS.'index_admin/','一覧へ');
		$action['order'] = 10;
		\Actionset::add_actionset($this->request->controller, $this->request->module, 'ctrl', $action);

		//view
		$content->set_global('title', $title);
		$content->set_global('item', $obj, false);
		$content->set_global('form', $form, false);
		$this->template->content = $content;
		\Auth_Acl_Locomoacl::set_item($this);
		$content->base_assign($obj);
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




}
