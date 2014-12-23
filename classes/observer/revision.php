<?php
namespace Locomo;
class Observer_Revision extends \Orm\Observer
{
	static $delete = false;
	static $create = false;

	/**
	 * __construct
	 */
	public function __construct($class)
	{
	}

	/**
	 * after_insert()
	 */
	public function after_insert(\Orm\Model $obj)
	{
		$args = $this->insert_revision($obj, 'create');
		static::$create = true;
	}

	/**
	 * after_save()
	 */
	public function after_save(\Orm\Model $obj)
	{
		//delete、createの際は、saveが走るので、その抑止
		if (static::$delete || static::$create) return;

		$operation = 'update';

		/*
		//復活 - after_save()で復活は捕まえられない。model_softにobserver追加を希望するか？
		//あるいはrevisionテーブルをみて、直前のoperationと比較するか
		if (isset($obj->deleted_at) && is_null($obj->deleted_at)):
			$originals = $obj->get_original_values();
			if ( ! is_null($originals['deleted_at'])):
				$operation = 'undelete';
			endif;
		endif;

		//直近のoperationを取得
		$option = array(
		'select' => array('operation'),
		'where' => array(
			array('pk_id', $obj->id),
		),
		'order_by' => 'created_at',
		);
		$last = \Model_Revision::find('last',$option);

だが、削除->編集の流れの後復活したらこれもとれないのでNG
		*/

		$args = $this->insert_revision($obj, $operation);
	}

	/**
	 * before_delete()
	 */
	public function before_delete(\Orm\Model $obj)
	{
		//本当はafter_deleteをとりたいが、after_deleteではprimary keyが消えているので、loggingできない
		$operation = 'delete';
		if (isset($obj->deleted_at) && ! is_null($obj->deleted_at)):
			$operation = 'purge';
		endif;
		$args = $this->insert_revision($obj, $operation);
		static::$delete = true;
	}

	/**
	 * insert_revision($obj, $operation = '')
	 */
	public function insert_revision($obj, $operation = '')
	{
		static $counter = 0;

		$tmp = (object) array();

		// $objしたものをそのままserialize()するとunserialize()したときに__PHP_Incomplete_Classになってしまうので、いったん別のobjectにする。
		$primary_key = $obj::get_primary_keys('first');
		$properties = $obj::properties();

		// is_locomo_bulk
		$vals = array();
		$posts = \Input::post();
		if(isset($posts['is_locomo_bulk']))
		{
			foreach ($posts as $k => $post)
			{
				foreach ($properties as $kk => $vv)
				{
					if( ! isset($post[$kk])) continue;
					$vals[$k][$kk] = $post[$kk];
				}
			}
		}
		else
		{
			if( ! empty($posts)) $vals[] = $posts;
		}

		// empty vals means this is added by add_testdata()
		if (empty($vals))
		{
			foreach ($properties as $k => $vv)
			{
				$vals[1][$k] = $obj->$k;
			}
		}

		// args
		foreach ($vals as $val)
		{
			$args = array();
			$args['model']      = \Inflector::add_head_backslash(get_class($obj));
			$args['pk_id']      = $obj->$primary_key;
			$args['data']       = serialize($val);
			$args['comment']    = \Input::post('revision_comment') ?: '';
			$args['created_at'] = date('Y-m-d H:i:s');
			$args['operation']  = $operation;
			$args['user_id']    = \Auth::get('id');
	
			// save revision
			$model = \Model_Revision::forge($args);
			$model->insert_revision();
		}
		$counter++;
	}

	
}
