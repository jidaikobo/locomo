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
	 * find_item_anyway($id)
	 *
	 * @param int   $id
	 *
	 * @return object | result
	 * @author shibata@jidaikobo.com
	 */
	public static function find_item_anyway($id = null)
	{
		if(empty($id)) return false;

		//とにかく項目を取得する
		$primary_key = static::$_primary_key[0];
		$q = \DB::select($primary_key);
		$q->from(static::$_table_name);
		$q->where($primary_key, $id);
		$item = self::find($id);
		$item = $item ?: self::find_deleted($id);
		return $item;
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

		//まず取得（表示非表示を問わず取得する）
		$item = self::find_item_anyway($id);
		if( ! $item) return false;

		//表示制限要件がなければ許可
		if(
			! isset($item->status) &&
			! isset($item->deleted_at) &&
			! isset($item->created_at) &&
			! isset($item->expired_at)
		):
			return $item;
		endif;

		//要件のないmodelであれば常にtrue
		$status     = isset($item->status)     ? $item->status     : 'public';
		$deleted_at = isset($item->deleted_at) ? $item->deleted_at : null;
		$created_at = isset($item->created_at) ? $item->created_at : time() - 64800;
		$expired_at = isset($item->expired_at) ? $item->expired_at : time() + 64800;

		//判定用諸情報
		$controller = \Inflector::denamespace(\Request::main()->controller);
		$controller = strtolower(substr($controller, 11));
		$action     = \Request::active()->action;
		$userinfo   = \User\Controller_User::$userinfo;

		//まず一般表示権限を確認
		if(
			\Acl\Controller_Acl::auth($controller, 'view', $userinfo) &&
			$status != 'revision' &&
			$status != 'invisible' &&
			$deleted_at == null &&
			strtotime($created_at) <= time() &&
			strtotime($expired_at) >= time()
		):
			return $item;
		endif;

		//削除された項目を確認
		if(
			\Acl\Controller_Acl::auth($controller, 'view_deleted', $userinfo) &&
			$status != 'revision' &&
			$deleted_at != null
		):
			return $item;
		endif;

		//期限切れ項目を確認
		if(
			\Acl\Controller_Acl::auth($controller, 'view_expired', $userinfo) &&
			$status != 'revision' &&
			$deleted_at != null &&
			strtotime($expired_at) <= time()
		):
			return $item;
		endif;

		//予約項目を確認
		if(
			\Acl\Controller_Acl::auth($controller, 'view_yet', $userinfo) &&
			$status != 'revision' &&
			$deleted_at == null &&
			strtotime($created_at) >= time() &&
			strtotime($expired_at) >= time()
		):
			return $item;
		endif;

		//リビジョンを確認
		if(
			\Acl\Controller_Acl::auth($controller, 'view_revision', $userinfo) &&
			$status == 'revision'
		):
			return $item;
		endif;

		//不可視項目を確認
		if(
			\Acl\Controller_Acl::auth($controller, 'view_invisible', $userinfo) &&
			$status == 'invisible'
		):
			return $item;
		endif;

		//オーナ権限を確認（\Acl\Controller_Acl::owner_auth()でないことに注意。このチェックはコントローラ依存する）
		$request = \Request::forge();
		$current_controller = '\\'.\Request::main()->controller;
		$current_controller_obj = new $current_controller($request);
		if(
			$current_controller_obj->check_owner_acl($userinfo, $item)
		):
			return $item;
		endif;

		return false ;
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

		//mode - date
		$now = date('Y-m-d H:i:s', time());
		if(@$args['mode'] == 'deleted' && \DBUtil::field_exists(static::$_table_name, array('deleted_at'))):
			$q->where('deleted_at', '!=', '');
		elseif(@$args['mode'] == 'yet' && \DBUtil::field_exists(static::$_table_name, array('created_at'))):
			$q->where('created_at', '>', $now);
		elseif(@$args['mode'] == 'expired' && \DBUtil::field_exists(static::$_table_name, array('expired_at'))):
			$q->where('expired_at', '<', $now);
		else:
			if(\DBUtil::field_exists(static::$_table_name, array('created_at')))
				$q->where('created_at', '<=', $now);
			if(\DBUtil::field_exists(static::$_table_name, array('expired_at')))
				$q->where('expired_at', '>=', $now);
			if(\DBUtil::field_exists(static::$_table_name, array('deleted_at')))
				$q->where('deleted_at', '=', null);
		endif;

		//mode - status
		if(\DBUtil::field_exists(static::$_table_name, array('status'))):
			if(@$args['mode'] == 'revision'):
				$q->where('status', '=', 'revision');
			elseif(@$args['mode'] == 'invisible'):
				$q->where('status', '=', 'invisible');
			endif;
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
