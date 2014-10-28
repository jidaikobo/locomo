<?php
namespace Revision;
class Model_Revision extends \Locomo\Model_Base
{
	protected static $_table_name = 'revisions';

	protected static $_properties = array(
		'id',
		'model',
		'pk_id',
		'data',
		'comment',
		'operation',
		'created_at',
		'deleted_at',
	);

	protected static $pagination_config = array(
		'uri_segment' => 4,
		'num_links' => 5,
		'per_page' => 10,
		'template' => array(
			'wrapper_start' => '<div class="pagination">',
			'wrapper_end' => '</div>',
			'active_start' => '<span class="current">',
			'active_end' => '</span>',
		),
	);

	/**
	 * find_all_revisions()
	*/
	public static function find_all_revisions($offset, $view, $model)
	{
		//vals
		$model_str = substr($model,0,1) == '\\' ? substr($model,1) : $model;

		//model information
		$table = \Inflector::tableize($model);
		$subject = $model::get_default_field_name('subject');
		$subject = $table.'.'.$subject;
		$pk = $table.'.'.$model::get_primary_keys('first');

		//vals
		$pagination_config = self::$pagination_config;
		$offset = $offset ?: \Pagination::get('offset');
		$limit  = \Input::get('limit')  ?: $pagination_config['per_page'];
		$likes  = \Input::get('likes')  ?: null ;

		//リビジョンの一覧を取得
		$q = \DB::select(
			'revisions.model',
			'revisions.pk_id',
			$subject,
			'revisions.comment',
			'revisions.operation',
			'revisions.created_at',
			'revisions.modifier_id'
		);
		$q->from('revisions');
		$q->where('model', $model_str);

		//like
		if($likes):
			$columns = \DB::list_columns('revisions');
			$q->and_where_open();
			foreach($columns as $field => $v):
				$q->or_where('revisions.'.$field, 'like', '%'.$likes['all'].'%');
			endforeach;
			$q->and_where_close();
		endif;

		//group by
		$q->group_by('pk_id');
		$q->order_by('pk_id', 'ASC');
		$q->join($table);
		$q->on($pk, '=', 'revisions.pk_id');

		//count
		$count = $q->execute()->count();

		//num
		if( $limit ) $q->limit($limit);
		if( $offset ) $q->offset($offset);

		//retval
		$items = $q->as_object($model)->execute()->as_array() ;

		//pagination
		$pagination_config['total_items'] = $count;
		$pagination_config['pagination_url'] = \Uri::create('/'.\Request::main()->module.'/index_revision/'.\Inflector::singularize($table).'/', array(), \Input::get());
		\Pagination::set_config($pagination_config);

		//assign
		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', $count);
		$view->set('items', $items);

		return $view;
	}

	/**
	 * find_revisions()
	*/
	public static function find_revisions($model = null, $pk_id = null)
	{
		if(is_null($model) || is_null($pk_id)) \Response::redirect($this->request->module);

		//リビジョンの一覧を取得
		$q = \DB::select('*');
		$q->from('revisions');
		$q->where('model', $model);
		$q->where('pk_id', $pk_id);
		$items = $q->as_object()->execute()->as_array();

		//dataをunserialize（一覧表にsubjectの変遷を出すため）
		foreach($items as $k => $item):
			$items[$k]->data = unserialize($item->data);
		endforeach;

		return $items;
	}

	/**
	 * find_revision()
	*/
	public static function find_revision($id = null)
	{
		is_null($id) and \Response::redirect($this->request->module);

		//リビジョンを取得
		$q = \DB::select('*');
		$q->from('revisions');
		$q->where('id', $id);
		return $q->as_object()->execute()->current();
	}

	/**
	 * find_options_revisions()
	*/
	public static function find_options_revisions($optname = null)
	{
		if(is_null($optname)) \Response::redirect(\Uri::base());

		//リビジョンの一覧を取得
		$q = \DB::select('*');
		$q->from('revisions');
		$q->where('model', $optname);
		return $q->as_object()->execute()->as_array();
	}

	/**
	 * find_options_revision()
	*/
	public static function find_options_revision($optname = null, $datetime = null)
	{
		if(is_null($optname) || is_null($datetime)) \Response::redirect(\Uri::base());
		$datetime = date('Y-m-d H:i:s', $datetime);

		//リビジョンを取得
		$q = \DB::select('*');
		$q->from('revisions');
		$q->where('model', $optname);
		$q->where('created_at', $datetime);
		return $q->as_object()->execute()->current();
	}

	/**
	 * insert_revision()
	*/
	public function insert_revision()
	{
		//当該コンテンツの最新データを取得
		$q = \DB::select('created_at','operation');
		$q->from('revisions');
		$q->where('model', $this->model);
		$q->where('pk_id', $this->pk_id);
		$q->order_by('created_at', 'DESC');
		$result = $q->execute()->current();
		$created_at = $result['created_at'];
		$operation = $result['operation'];

		//configからrevision間隔を取得
		$config_path = PKGCOREPATH.'modules/revision/config/revision.php';
		$config_path_default = PKGPROJPATH.'modules/revision/config/revision.php';
		$config = file_exists($config_path) ? \Config::load($config_path) : \Config::load($config_path_default);

		//operationが異なる場合は、絶対に保存する
		$force_save = $this->operation != $operation ? true : false;

		//最新データと規定時間との比較 - $created_at がゼロのときは初めて
		if(
			! $force_save &&
			$created_at && strtotime($created_at) >= time() - intval($config['revision_interval']) &&
			empty($this->comment)
		):
			return;
		endif;

		//保存
		$this->save();

		return;
	}
}