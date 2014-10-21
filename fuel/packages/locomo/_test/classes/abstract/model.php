<?php
namespace Locomo;
abstract class Model extends \Orm\Model_Soft
{
	/**
	 * get_table_name()
	 *
	 * @return  str
	 * @author shibata@jidaikobo.com
	 */
	public static function get_table_name()
	{
		return static::$_table_name;
	}

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
	 * find_item()
	 *
	 * @param int   $id
	 *
	 * @return object | result
	 * @author shibata@jidaikobo.com
	 */
	public static function find_item($id)
	{
		if(empty($id)) return false;

		//acl確認

		//retval
		$obj = (object) array();

		//start query building
		$q = \DB::select('id');
		$q->from(static::$_table_name);
		$q->where('id', $id);
		$q->where('deleted_at', '=', null);
/*
あとで未来と期限切れを排除
*/
		$id = $q->as_object()->execute()->current() ;

		//item
		return self::find($id) ;
	}

	/**
	 * delete_relations()
	 * @param object $id
	 */
	public static function delete_relations($id)
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
	 * prepare_relations()
	 * 
	 * ugly code... :-(
	 * 
	 * @param object $obj
	 * @return object
	 * @author shibata@jidaikobo.com
	 */
	public static function prepare_relations($obj, $args = array())
	{
		//args
		$args = \Input::post() ? \Input::post() : $args ;
		if(empty($args)) return false;

		//add fields to obj
		foreach(self::get_table_relations() as $relation_type => $relation):
			if(empty($relation)) continue;
			if($relation_type == 'belongs_to') continue;

			//building object
			foreach($relation as $field => $each_relation):
					if( ! isset($args[$field])) continue;

					$model_to = $each_relation["model_to"] ;
					$column = key($args[$field]);

					//for has_many
					//has_many は都度対象モデルをforgeして、値を入れていかないとうまく入らない？
					if(is_array($args[$field][$column])):
/*
						foreach($args[$field] as $each_set):
							foreach($each_set as $each_value):
								$tmp = $model_to::forge() ;
								
								if(isset($each_relation['conditions']['where'])):
									foreach($each_relation['conditions']['where'] as $condition):
										$tmp->$condition[0] = $condition[2];
									endforeach;
								endif;
								
								//key_from and key_to
								$tmp->$each_relation['key_to'] = $obj->$each_relation['key_from'];
								
								//columns
								$column4has_many = key($args[$field][$column]);
								$tmp->$column4has_many = $each_value;
								
								$obj->{$field}[] = $tmp;
							endforeach;
						endforeach;
*/
					else:
					//has_one
						$tmp = $model_to::forge() ;
	
						if(isset($each_relation['conditions']['where'])):
							foreach($each_relation['conditions']['where'] as $condition):
								$tmp->$condition[0] = $condition[2];
							endforeach;
						endif;
	
						//key_from and key_to
						$tmp->$each_relation['key_to'] = $obj->$each_relation['key_from'];

						//column
						$tmp->$column = $args[$field];
						$obj->{$field} = $tmp;
					endif;

			endforeach;
		endforeach;


echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;">' ;
var_dump( $obj ) ;
echo '</textarea>' ;
die();

		return $obj;

	}

	/**
	 * find_items()
	 *
	 * @param array $args
	 * @param str   $args[mode] [deleted]
	 *
	 * below arguments are override by \Input::get('same name of args').
	 *
	 * @param int   $args[limit]
	 * @param int   $args[offset]
	 * @param array $args[orders] &orders[fieldname]=asc|desc
	 * @param array $args[searches] &searches[fieldname]=text
	 * @param array $args[likes] &likes[fieldname]=text | &likes[all]=text
	 *
	 * @return object | count, results
	 * @author shibata@jidaikobo.com
	 */
	public static function find_items($args = array())
	{
		//acl確認
		

		//args
		$is_deleted = (@$args['mode'] == 'deleted') ;
		$limit      = @intval( $args['limit'] )    ?: false ;
		$offset     = @intval( $args['offset'] )   ?: 0 ;
		$orders     = @intval( $args['orders'] )   ?: array() ;
		$searches   = @intval( $args['searches'] ) ?: array() ;
		$likes      = @intval( $args['likes'] )    ?: array() ;

		//user request
		$limit    = \Input::get('limit')    ?: $limit ;
		$offset   = \Input::get('offset')   ?: $offset ;
		$orders   = \Input::get('orders')   ?: $orders ;
		$searches = \Input::get('searches') ?: $searches ;
		$likes    = \Input::get('likes')    ?: $likes ;

		//retval
		$obj = (object) array();

		//start query building
		$q = \DB::select();
		$q->from(static::$_table_name);

		//order
		if($orders):
			foreach($orders as $field => $order):
				if( ! \DBUtil::field_exists(static::$_table_name, array($field))) continue;
				$q->order_by($field, $order);
			endforeach;
		endif;

		//search
		if($searches):
			foreach($searches as $field => $keyword):
				if( ! \DBUtil::field_exists(static::$_table_name, array($field))) continue;
				$q->where($field, '=', $keyword);
			endforeach;
		endif;

		//like
		if($likes):
			if(array_key_exists ('all', $likes)):
				$columns = \DB::list_columns(static::$_table_name);
				$q->and_where_open();
				foreach($columns as $field => $v):
					$q->or_where($field, 'like', '%'.$likes['all'].'%');
				endforeach;
				$q->and_where_close();
			else:
				foreach($likes as $field => $keyword):
					if( ! \DBUtil::field_exists(static::$_table_name, array($field))) continue;
					$q->where($field, 'like', '%'.$keyword.'%');
				endforeach;
			endif;
		endif;

		//is_deleted
		if($is_deleted):
			$q->where('deleted_at', '!=', '');
		else:
			$q->where('deleted_at', '=', null);
		endif;

		//count all before limit
		$obj->count = $q->as_object()->execute()->count() ;

		//num
		if( $limit ) $q->limit($limit);
		$q->offset($offset);

		//results
		$obj->results = $q->as_object()->execute()->as_array() ;

		return $obj ;
	}
}
