<?php
namespace Kontiki;

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
	 * get_original_values()
	 *
	 * @return  array
	 * @author shibata@jidaikobo.com
	 */
	public function get_original_values()
	{
		return $this->_original;
	}

	/**
	 * find_item()
	 *
	 * @param int   $id
	 *
	 * @return object | result
	 * @author shibata@jidaikobo.com
	 */
	public static function find_item($id = null)
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

		$q->where('created_at', '<=', date('Y-m-d H:i:s'));
//		$q->where('expired_at', '>=', date('Y-m-d H:i:s'));
/*
あとで未来と期限切れを排除
*/
		$id = $q->as_object()->execute()->current() ;

		//item
		return self::find($id) ;
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
		$limit      = @intval( $args['limit'] )    ?: false ;
		$offset     = @intval( $args['offset'] )   ?: 0 ;
		$orders     = @intval( $args['orders'] )   ?: array() ;
		$searches   = @intval( $args['searches'] ) ?: array() ;
		$likes      = @intval( $args['likes'] )    ?: array() ;
		$type       = @in_array( $args['type'], array('array','object') ) ? $args['type'] : false ;

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

		//mode
		$now = date('Y-m-d H:i:s', time());
		if(@$args['mode'] == 'deleted'):
			$q->where('deleted_at', '!=', '');
		elseif(@$args['mode'] == 'yet'):
			$q->where('created_at', '>', $now);
		elseif(@$args['mode'] == 'expired'):
			$q->where('expired_at', '<', $now);
		else:
			$q->where('created_at', '<=', $now);
			$q->where('expired_at', '>=', $now);
			$q->where('deleted_at', '=', null);
		endif;

		//count all before limit
		if($type == 'array'):
			$obj->count = $q->execute()->count() ;
		else:
			$obj->count = $q->as_object()->execute()->count() ;
		endif;

		//num
		if( $limit ) $q->limit($limit);
		if( $offset ) $q->offset($offset);

		//results
		if($type == 'array'):
			$obj->results = $q->execute()->as_array() ;
		else:
			$obj->results = $q->as_object()->execute()->as_array() ;
		endif;

		return $obj ;
	}
}
