<?php
namespace Revision;
class Model_Revision extends \Locomo\Model_Base
{
	protected static $_table_name = 'revisions';
	protected static $_modifiers = array(
		'-2' => 'ルート管理者',
		'-1' => '管理者',
		'0'  => 'ゲスト',
	);

	protected static $_properties = array(
		'id',
		'model',
		'pk_id',
		'data',
		'comment',
		'operation',
		'created_at',
		'deleted_at',
		'modifier_id',
	);

	protected static $pagination_config = array(
		'uri_segment' => 4,
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
	 * find_all_revisions()
	 * \DB::Expr()があるためpaginated_find()が使えない
	*/
	public static function find_all_revisions($view, $model, $opt)
	{
		//vals
		if ( ! class_exists($model)) return false;
		$model_str = substr($model,0,1) == '\\' ? substr($model,1) : $model;
		$likes = \Input::get('likes')  ?: null ;
		$range = isset($opt['range']['where']) ? $opt['range']['where'] : null ;
		$order = isset($opt['range']['order_by']) ? $opt['range']['order_by'] : null ;

		//model information
		$table = \Inflector::tableize($model);
		$subject = $model::get_default_field_name('subject');
		$subject = $table.'.'.$subject;
		$pk = $table.'.'.$model::get_primary_keys('first');

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
		$q->from($table);
		$q->where('revisions.model', $model_str);

		//like
		if ($likes):
			$columns = \DB::list_columns('revisions');
			$q->and_where_open();
			foreach($columns as $field => $v):
				$q->or_where('revisions.'.$field, 'like', '%'.$likes['all'].'%');
			endforeach;
			$q->and_where_close();
		endif;

		//group by
		$q->group_by('revisions.pk_id');

		//join
		$q->where($pk, '=', \DB::Expr('`revisions`.pk_id'));

		//opt
		if ($range){
			//where句を確認
			foreach($range as $r):
				list($field, $op, $val) = $r == 2 ?
					array($table.'.'.$r[0], '=', $r[1]) :
					array($table.'.'.$r[0], $r[1], $r[2]);
				$q->where($field, $op, $val);
			endforeach;
		}

		//order
		if ($order){
			foreach($order as $r):
				list($order_by, $order) = $r == 1 ?
					array($table.'.'.$r[0], 'ASC') :
					array($table.'.'.$r[0], $r[1]);
				$q->order_by($order_by, $order);
			endforeach;
		}else{
			$q->order_by('revisions.pk_id', 'ASC');
		}

		//count
		$count = $q->execute()->count();

		//pagination
		$pagination_config = self::$pagination_config;
		$pagination_config['total_items'] = $count;
		$pagination_config['pagination_url'] = \Uri::create('/'.\Request::main()->module.'/index_revision/'.\Inflector::singularize($table).'/', array(), \Input::get());
		\Pagination::set_config($pagination_config);
		$offset = \Pagination::get('offset');
		$limit  = \Input::get('limit')  ?: $pagination_config['per_page'];

		//num
		if ( $limit ) $q->limit($limit);
		if ( $offset ) $q->offset($offset);

		//retval
		$items = $q->as_object($model)->execute()->as_array() ;

		//items
		foreach($items as $k => $item):
			$modifier_name = \User\Model_User::find($item->modifier_id, array('select'=>array('display_name')));
			$modifier_name = $modifier_name ? $modifier_name : static::$_modifiers[$item->modifier_id];

			$items[$k]->modifier_name = $modifier_name;
		endforeach;

		//assign
		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', $count);
		$view->set('items', $items);

		return $view;
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
		$config_path = LOCOMOPATH.'modules/revision/config/revision.php';
		$config_path_default = APPPATH.'modules/revision/config/revision.php';
		$config = file_exists($config_path) ? \Config::load($config_path) : \Config::load($config_path_default);

		//operationが異なる場合は、絶対に保存する
		$force_save = $this->operation != $operation ? true : false;

		//最新データと規定時間との比較 - $created_at がゼロのときは初めて
		//コメントがあるときにも保存する
		if (
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