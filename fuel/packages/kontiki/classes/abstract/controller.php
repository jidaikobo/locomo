<?php
namespace Kontiki;
abstract class Controller extends \Fuel\Core\Controller_Rest
{
	/**
	* @var string name for human
	*/
	public static $nicename = '';

	/**
	 * @var string model name
	 */
	protected $model_name  = '';

	/**
	 * @var string table name
	 */
	protected $table_name  = '';

	/**
	 * @var array set by self::set_actionset()
	 */
	public static $actionset  = array();

	/**
	 * @var array default languages of flash messages
	 */
	protected $messages = array(
		'auth_error'       => 'You are not permitted.',
		'view_error'       => 'Could not find %1$s #%2$d.',
		'create_success'   => 'Added %1$s #%2$d.',
		'create_error'     => 'Could not save %1$s.',
		'edit_success'     => 'Updated %1$s #%2$d.',
		'edit_error'       => 'Could not update %1$s #%2$d.',
		'delete_success'   => 'Deleted %1$s #%2$d.',
		'delete_error'     => 'Could not delete %1$s #%2$d.',
		'undelete_success' => 'Undeleted %1$s #%2$d.',
		'undelete_error'   => 'Could not undelete %1$s #%2$d.',
		'purge_success'    => 'Completely deleted %1$s #%2$d.',
		'purge_error'      => 'Could not delete %1$s #%2$d.',
	);

	/**
	 * @var array default languages of page title
	 */
	protected $titles = array(
		'index'          => '%1$s.',
		'view'           => '%1$s.',
		'create'         => 'Create %1$s.',
		'edit'           => 'Edit %1$s.',
		'index_deleted'  => 'Delete List %1$s.',
		'index_yet'      => 'Yet List %1$s.',
		'index_expired'  => 'Expired List %1$s.',
		'view_deleted'   => 'Deleted %1$s.',
		'edit_deleted'   => 'Edit Deleted %1$s.',
		'confirm_delete' => 'Are you sure to Permanently Delete a %1$s?',
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
	* @var test datas
	* array fieldname => 'type(text|email|int|date|datetime|geometry)'
	*/
	protected $test_datas = array();

	/**
	* before()
	*/
	public function before()
	{
		//acl
		$group_id = -2;//test data
		if( ! \Acl\Controller_Acl::auth($this->request->module, $this->request->action, $group_id) ):
			\Session::set_flash('error', $this->messages['auth_error']);
			\Response::redirect(\Uri::base(false));
		endif;

		//actionset
		$this->set_actionset();

		//vals
		$this->model_name  = '\\'.ucfirst($this->request->module).'\\Model_'.ucfirst($this->request->module);
		$model = $this->model_name ;
		$this->table_name = $model::get_table_name();
		parent::before();
	}

	/**
	 * set_actionset()
	 * 
	 */
	public function set_actionset()
	{
		self::$actionset = \Kontiki\Actionset::actionItems($this->request->module);
	}

	/**
	 * action_add_testdata()
	 * 
	 */
	public function action_add_testdata($num = 10)
	{
		//only at development
		if(\Fuel::$env != 'development') die();

		//$test_datas
		if(empty($this->test_datas)):
			\Session::set_flash('error', 'need to prepare test_data proparty.');
			\Response::redirect($this->request->module);
		endif;

		$model = $this->model_name ;

		for($n = 1; $n <= $num; $n++):
			foreach($this->test_datas as $k => $v):
				switch($v):
					case 'text':
						$val = $this->request->module.'-'.$k.'-'.md5(microtime()) ;
						break;
					case 'email':
						$val = $this->request->module.'-'.$k.'-'.md5(microtime()).'@example.com' ;
						break;
					case 'int':
						$val = 1 ;
						break;
					case 'date':
						$val = date('Y-m-d') ;
						break;
					case 'datetime':
						$val = date('Y-m-d H:i:s') ;
						break;
					case 'geometry':
						$val = "GeomFromText('POINT(138.72777769999993 35.3605555)')" ;//Mt. fuji
						break;
				endswitch;
				$args[$k] = $val;
			endforeach;
			$obj = $model::forge($args);
			$obj->save();
		endfor;
		\Session::set_flash('success', 'added '.$num.' datas.');
		\Response::redirect($this->request->module);
	}

	/**
	 * index_core()
	 * 
	 * 将来的には、任意のテンプレートを指定し、任意の項目一覧をウィジェットっぽく使える？
	 * 
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
		$view->set_global('title', sprintf($this->titles[$action], $this->request->module));

		return \Response::forge($view);
	}

	/**
	 * action_index()
	 * 
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
	 * 
	 */
	public function action_view($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect($this->request->module);

		if ( ! $data['item'] = $model::find_item($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], $this->request->module, $id)
			);
			\Response::redirect($this->request->module);
		endif;

		//view
		$view = \View::forge('view');
		$view->set('item', $data['item']);
		$view->set_global('title', sprintf($this->titles['view'], $this->request->module));

		return \Response::forge($view);
	}

	/**
	 * pre_save_hook()
	 * 
	 */
	public function pre_save_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);
		return $obj;
	}

	/**
	 * post_save_hook()
	 * 
	 */
	public function post_save_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);
		return $obj;
	}

	/**
	 * pre_reinput_view_hook()
	 * 
	 */
	public function pre_reinput_view_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);
		return $obj;
	}

	/**
	 * view_hook()
	 * 
	 */
	public function view_hook($obj = null, $mode = 'edit')
	{
		if($obj == null) \Response::redirect($this->request->module);
		return $obj;
	}

	/**
	 * action_create()
	 * 
	 */
	public function action_create()
	{
		$model = $this->model_name ;
		if (\Input::method() == 'POST'):
			$val = $model::validate('create');
			if ($val->run()):
				$args = array();
				foreach(\Input::post() as $field => $value):
					if( ! \DBUtil::field_exists($this->table_name, array($field))) continue;
					$args[$field] = $value;
				endforeach;

				$obj = $model::forge($args);

				if ($obj and $obj->save()):
					
					//save relations
//					$obj = $model::insert_relations($obj->id);
					
					\Session::set_flash(
						'success',
						sprintf($this->messages['create_success'], $this->request->module, $obj->id)
					);
					\Response::redirect($this->request->module);
				else:
					\Session::set_flash(
						'error', sprintf($this->messages['create_error'],
						$this->request->module)
					);
				endif;
			else:
				\Session::set_flash('error', $val->error());
			endif;
		endif;

		//view
		$view = \View::forge('create');
		$view->set_global('title', sprintf($this->titles['create'], $this->request->module));

		return \Response::forge($view);
	}

	/**
	 * edit_core()
	 * 
	 */
	public function edit_core($id = null, $obj = null, $redirect = null, $title = null)
	{
		if($id == null || $obj == null || $redirect == null) \Response::redirect($this->request->module);

		$model = $this->model_name ;
		$val = $model::validate('edit',$id);
		$view = \View::forge('edit');
		$obj = $this->view_hook($obj, 'edit');

		//validation succeed
		if ($val->run()):
			//prepare self fields
			foreach(\Input::post() as $field => $value):
				if( ! \DBUtil::field_exists($this->table_name, array($field))) continue;
				$obj->$field = $value;
			endforeach;

			//pre_save_hook
			$obj = $this->pre_save_hook($obj, 'edit');

			//save
			if ($obj->save()):

				//post_save_hook
				$obj = $this->post_save_hook($obj, 'edit');

				//save relations
				$model::delete_relations($obj->id);
				$obj = $model::insert_relations($obj->id);

				//message
				\Session::set_flash(
					'success',
					sprintf($this->messages['edit_success'], $this->request->module, $id)
				);
				\Response::redirect($redirect);
			else:
				\Session::set_flash(
					'error',
					sprintf($this->messages['edit_error'], $this->request->module, $id)
				);
			endif;
		//edit view or validation failed
		else:
			if (\Input::method() == 'POST'):
				foreach(\Input::post() as $k => $v):
					if($k == 'submit') continue;
					$obj->$k = $v;
				endforeach;

				//pre_reinput_view_hook()
				$obj = $this->pre_reinput_view_hook($obj, 'edit');

				\Session::set_flash('error', $val->error());
			endif;

			$view->set_global('item', $obj, false);
		endif;

		//view
		$view->set_global('title', sprintf($this->titles['edit'], $this->request->module));

		return \Response::forge($view);
	}

	/**
	 * action_edit()
	 * 
	 */
	public function action_edit($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect($this->request->module);

		if ( ! $obj = $model::find($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], $this->request->module, $id)
			);
			\Response::redirect($this->request->module);
		endif;
		$title = sprintf($this->titles['edit'], $this->request->module);

		//edit core
		return $this->edit_core($id, $obj, $this->request->module.'/edit/'.$id, $title);
	}

	/**
	 * action_delete()
	 * 
	 */
	public function action_delete($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect($this->request->module);

		if ($obj = $model::find_item($id)):
			$obj->delete();
			\Session::set_flash(
				'success',
				sprintf($this->messages['delete_success'], $this->request->module, $id)
			);
		else:
			\Session::set_flash(
				'error',
				sprintf($this->messages['delete_error'], $this->request->module, $id)
			);
		endif;

		return \Response::redirect($this->request->module);
	}

	/**
	 * action_index_deleted()
	 * 
	 */
	public function action_index_deleted($pagenum = 1)
	{
		$args = array(
			'pagenum' => $pagenum,
			'action'  => 'index_deleted',
			'mode'    => 'deleted',
		);
		return self::index_core($args);
	}

	/**
	 * action_index_yet()
	 * 
	 */
	public function action_index_yet($pagenum = 1)
	{
		$args = array(
			'pagenum' => $pagenum,
			'action'  => 'index_yet',
			'mode'    => 'yet',
		);
		return self::index_core($args);
	}

	/**
	 * action_index_expired()
	 * 
	 */
	public function action_index_expired($pagenum = 1)
	{
		$args = array(
			'pagenum' => $pagenum,
			'action'  => 'index_expired',
			'mode'    => 'expired',
		);
		return self::index_core($args);
	}

	/**
	 * action_view_deleted()
	 * 
	 */
	public function action_view_deleted($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect($this->request->module);

		if ( ! $data['item'] = $model::find_deleted($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], $this->request->module, $id)
			);
			\Response::redirect($this->request->module);
		endif;

		//view
		$view = \View::forge('view');
		$view->set('is_deleted', true);
		$view->set('item', $data['item']);
		$view->set_global('title', sprintf($this->titles['view_deleted'], $this->request->module));

		return \Response::forge($view);
	}

	/**
	 * action_edit_deleted()
	 * 
	 */
	public function action_edit_deleted($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect($this->request->module);

		if ( ! $obj = $model::find_deleted($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], $this->request->module, $id)
			);
			\Response::redirect($this->request->module);
		endif;
		$title = sprintf($this->titles['edit'], $this->request->module);
		return $this->edit_core($id, $obj, $this->request->module.'/edit_deleted/'.$id, $title);
	}

	/**
	 * action_confirm_delete()
	 * 
	 */
	public function action_confirm_delete($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect($this->request->module);

		if ( ! $data['item'] = $model::find_deleted($id)):
			\Session::set_flash(
				'error',
				sprintf($this->messages['view_error'], $this->request->module, $id)
			);
			\Response::redirect($this->request->module);
		endif;

		//view
		$view = \View::forge('view');
		$view->set('item', $data['item']);
		$view->set('is_delete_deleted', true);
		$view->set_global('title', sprintf($this->titles['confirm_delete'], $this->request->module));

		return \Response::forge($view);
	}

	/**
	 * action_undelete()
	 * 
	 */
	public function action_undelete($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect($this->request->module);

		if ($obj = $model::find_deleted($id)):
			$obj->undelete();

			\Session::set_flash(
				'success',
				sprintf($this->messages['undelete_success'], $this->request->module, $id)
			);
		else:
			\Session::set_flash(
				'error',
				sprintf($this->messages['undelete_error'], $this->request->module, $id)
			);
		endif;

		\Response::redirect($this->request->module);
	}

	/**
	 * action_delete_deleted()
	 * 
	 */
	public function action_delete_deleted($id = null)
	{
		$model = $this->model_name ;
		is_null($id) and \Response::redirect($this->request->module);

		if ($obj = $model::find_deleted($id)):
			//なぜか削除されないが、親クラスである\Orm\Model::delete()のカスケーディング削除の恩恵にあずかるためには、これを使うべきっぽいので、とりあえずおいておく。
			$obj->purge();
			\Session::set_flash(
				'success',
				sprintf($this->messages['purge_success'], $this->request->module, $id)
			);
		else:
			\Session::set_flash(
				'error',
				sprintf($this->messages['purge_error'], $this->request->module, $id)
			);
		endif;

		return \Response::redirect($this->request->module.'/index_deleted/');
	}

	/**
	 * get_items()
	 * 
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
	 * 
	 */
	public function get_item()
	{
		if( ! ($id = intval( \Input::get('id')))) die();
		//view
		$model = $this->model_name ;
		$this->response($model::find_item($id));
	}
}
