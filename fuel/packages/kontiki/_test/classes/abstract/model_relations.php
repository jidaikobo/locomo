<?php
namespace Kontiki;

abstract class Model extends \Orm\Model_Soft
{
	/**
	 * get_table_relations()
	 *
	 * @return  array
	 * @author shibata@jidaikobo.com
	 */
	public static function get_table_relations()
	{
		$relations = array();
		$relations['has_one']    = isset(static::$_has_one)    ? static::$_has_one : array();
		$relations['has_many']   = isset(static::$_has_many)   ? static::$_has_many : array();
		$relations['many_many']  = isset(static::$_many_many)  ? static::$_many_many : array();
		$relations['belongs_to'] = isset(static::$_belongs_to) ? static::$_belongs_to : array();
		return $relations;
	}

	/**
	 * delete_relations()
	 * 
	 * チェックボックス等、空のときに$_POSTを送ってこないものは、チェックを外せないので、
	 * いったん関連レコードを抹消してから、値を書き込む。
	 * 
	 * @param object $id
	 */
	public static function delete_relations($id = null)
	{
		if(empty($id)) return false;

		//prepare related fields
		//check current model's relations
		foreach(self::get_table_relations() as $relation_type => $relation):
			if(empty($relation)) continue;
			if($relation_type == 'belongs_to') continue;

			//delete each relation's records
			foreach($relation as $field => $each_relation):
				
				$model_to = $each_relation["model_to"] ;
				$tmp = $model_to::forge();

				//query building for delete
				$q = \DB::delete();
				$q->table($tmp::get_table_name());
				if(isset($each_relation["conditions"])):
					foreach($each_relation["conditions"] as $condition):
						$q->where($condition);
					endforeach;
				endif;
				$q->where($each_relation['key_to'], '=', $id);
				$q->execute();
			endforeach;
		endforeach;
	}

	/**
	 * insert_relations()
	 * 
	 * ORMを活用して、controllerのsave()で一気に保存をしたいところだったが、
	 * save()で関連テーブルを保存しようとすると、関連テーブルにidフィールドが
	 * ないと、エラーになって保存ができない様子なので、とりあえずsave()を使わずに
	 * 関連テーブルを保存することにする。
	 * 
	 * @param object $obj
	 * @return void
	 * @author shibata@jidaikobo.com
	 */
	public static function insert_relations($id = null, $args = array())
	{
		//args
		$args = \Input::post() ? \Input::post() : $args ;
		if(empty($args)) return false;
		if(empty($id)) return false;

		//add fields to obj
		foreach(self::get_table_relations() as $relation_type => $relation):
			if(empty($relation)) continue;
			if($relation_type == 'belongs_to') continue;

			//building object
			foreach($relation as $field => $each_relation):
				if( ! isset($args[$field])) continue;

				$model_to = $each_relation["model_to"] ;
				$tmp_column = key($args[$field]);
				$table_name = $model_to::get_table_name();

				//for has_many
				if(is_array($args[$field][$tmp_column])):
					foreach($args[$field] as $each_args):
						$column = key($each_args);
						$value = $each_args[$column];
						self::query_insert_relations($id, $column, $value, $table_name, $each_relation);
					endforeach;
				else:
					//has_one
					$column = $tmp_column;
					$value = $args[$field][$column];
					self::query_insert_relations($id, $column, $value, $table_name, $each_relation);
				endif;
			endforeach;
		endforeach;
	}

	/**
	 * query_insert_relations()
	 * 
	 * @param object $obj
	 * @return object
	 * @author shibata@jidaikobo.com
	 */
	public static function query_insert_relations($id = null, $column = null, $value = null, $table_name = null, $relations = array())
	{
		$sets = array();
		$q = \DB::insert();
		$q->table($table_name);
		if(isset($relations["conditions"])):
			foreach($relations["conditions"] as $conditions):
				foreach($conditions as $condition):
					$sets[$condition[0]] = $condition[2];
				endforeach;
			endforeach;
		endif;
		$sets[$relations['key_to']] = $id;
		$sets[$column] = $value;
		$q->set($sets);
		$q->execute();
	}
}
