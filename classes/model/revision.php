<?php
namespace Locomo;
class Model_Revision extends \Model_Base
{
	protected static $_table_name = 'lcm_revisions';

	protected static $_properties = array(
		'id',
		'model',
		'pk_id',
		'data',
		'comment',
		'operation',
		'created_at',
		'deleted_at',
		'user_id' => array('default' => 0),
		'then_displayname',
	);

	protected static $_belongs_to = array(
		'user' => array(
			'key_from' => 'user_id',
			'model_to' => '\Model_Usr',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	/**
	 * _init
	 */
	 public static function _init()
	{
		// action can be deepen as controller needs
		$segment = array_search(\Request::main()->action, \Uri::segments());

		// 0:controller, 1:action_1. so add 2
		$segment = intval($segment) + 2;

		// properties
		\Arr::set(static::$pagination_config, 'uri_segment', $segment);

		// parent - this must be placed at the end of _init()
		parent::_init();
	}

	/**
	 * find_all_revisions()
	 * \DB::Expr()があるためpaginated_find()が使えない
	*/
	public static function find_all_revisions($view, $model)
	{
		//vals
		if ( ! class_exists($model)) return false;
		$model_str = \Inflector::add_head_backslash($model);
		$all = \Input::get('all')  ?: null ;
		$range = \Arr::get($model::$_options, 'where', null) ;
		$order = \Arr::get($model::$_options, 'order_by', null) ;

		//model information
		$table = $model::table();
		if( ! \DBUtil::table_exists($table)) return false;
		$subject = \Arr::get($model::get_field_by_role('subject'), 'lcm_field');
		$subject = $table.'.'.$subject; // relation table name
		$pk = $table.'.'.$model::primary_key()[0]; // relation table name

		//リビジョンの一覧を取得
		$q = \DB::select(
			'lcm_revisions.model',
			'lcm_revisions.pk_id',
			$subject,
			'lcm_revisions.comment',
			'lcm_revisions.operation',
			'lcm_revisions.created_at',
			'lcm_revisions.user_id'
		);
		$q->from('lcm_revisions');
		$q->from($table);
		$q->where('lcm_revisions.model', $model_str);

		//all
		if ($all):
			$columns = \DB::list_columns('lcm_revisions');
			$q->and_where_open();
			foreach($columns as $field => $v):
				$q->or_where('lcm_revisions.'.$field, 'like', '%'.$all.'%');
			endforeach;
			$q->or_where($subject, 'like', '%'.$all.'%');
			$q->and_where_close();
		endif;

		//group by
		$q->group_by('lcm_revisions.pk_id');

		//join
		$q->where($pk, '=', \DB::Expr('`lcm_revisions`.pk_id'));

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
			$q->order_by('lcm_revisions.pk_id', 'ASC');
		}

		//count
		$count = $q->execute()->count();

		//pagination
		$pagination_config = self::$pagination_config;
		$pagination_config['total_items'] = $count;
		$base_uri = join('/', array_slice(\Uri::segments(), 0, static::$pagination_config['uri_segment'] - 1));
		$pagination_config['pagination_url'] = \Uri::create($base_uri, array(), \Input::get());
		\Pagination::set_config($pagination_config);
		$offset = \Pagination::get('offset');
		$limit  = \Input::get('limit')  ?: $pagination_config['per_page'];

		//num
		if ( $limit ) $q->limit($limit);
		if ( $offset ) $q->offset($offset);

		//retval
		$items = $q->as_object($model)->execute()->as_array() ;

		//assign
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
		$q->from('lcm_revisions');
		$q->where('model', $this->model);
		$q->where('pk_id', $this->pk_id);
		$q->order_by('created_at', 'DESC');
		$result = $q->execute()->current();
		$created_at = $result['created_at'];
		$operation = $result['operation'];

		//configからrevision間隔を取得
		$config_path = APPPATH.'config/revision.php';
		$config_path_default = LOCOMOPATH.'config/revision.php';
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

	/**
	 * search_form()
	*/
	public static function search_form()
	{
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge('user', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// wrap
		$parent = parent::search_form_base('編集履歴一覧');
		$parent->add_after($form, 'customer', array(), array(), 'opener');

		return $parent;
	}
}
