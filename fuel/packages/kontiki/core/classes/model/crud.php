<?php
namespace Kontiki_Core;
class Model_Crud extends \Kontiki\Model_Base
{
	/**
	 * _primary_name
	 * to draw items title
	 *
	 */
	protected static $_primary_name = '';

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
	 * get_primary_key()
	 *
	 * @return  array
	 * @author shibata@jidaikobo.com
	 */
	public static function get_primary_key()
	{
		return static::$_primary_key;
	}

	/**
	 * get_primary_name()
	 *
	 * @return  str
	 * @author shibata@jidaikobo.com
	 */
	public static function get_primary_name()
	{
		return static::$_primary_name;
	}

	/**
	 * get_properties()
	 *
	 * @return  array
	 * @author shibata@jidaikobo.com
	 */
	public static function get_properties()
	{
		return static::$_properties;
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
	 * validate()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function validate($factory, $id = '')
	{
		$val = \Kontiki\Validation::forge($factory);
		return $val;
	}

	/**
	 * find_item_by_ctrl_and_id($controller = null, $id = null)
	 *
	 * @param str   $controller
	 * @param int   $id
	 *
	 * @return object | result
	 * @author shibata@jidaikobo.com
	 */
	public static function find_item_by_ctrl_and_id($controller = null, $id = null)
	{
		if(is_null($controller) || is_null($id)) return false;
		$modelname = \Kontiki\Util::get_valid_model_name($controller);
		$model     = $modelname::forge();
		return $model::find_item_anyway($id);
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

		//Orm_Softでとれる場合
		if(\DBUtil::field_exists(static::$_table_name, array('deleted_at'))):
			$item = self::find($id);
			$item = $item ?: self::find_deleted($id);
		else:
		//Orm_Softでとれない場合（deleted_atがないテーブル）
			$modelname = get_called_class();
			$q = \DB::select('*');
			$q->from(static::$_table_name);
			$q->where($primary_key, $id);
			$item = $q->as_object($modelname)->execute()->current();
		endif;
		return $item;
	}

	/**
	 * find_item()
	 *
	 * @param int   $id
	 * @param str   $mode
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

		//表示制限要件がいっさいなければ許可
		if(
			! isset($item->status) &&
			! isset($item->deleted_at) &&
			! isset($item->created_at) &&
			! isset($item->workflow_status) &&
			! isset($item->expired_at)
		):
			return $item;
		endif;

		//要件のないmodelであれば常にtrue
		$status     = isset($item->status)     ? $item->status     : 'public';
		$deleted_at = isset($item->deleted_at) ? $item->deleted_at : null;
		$created_at = isset($item->created_at) ? $item->created_at : date('Y-m-d H:i:s', time() - 64800);
		$expired_at = isset($item->expired_at) ? $item->expired_at : date('Y-m-d H:i:s', time() + 64800);

		//判定用諸情報
		$controller = \Inflector::denamespace(\Request::main()->controller);
		$controller = strtolower(substr($controller, 11));
		$action     = \Request::active()->action;
		$userinfo   = \User\Controller_User::$userinfo;

		//ワークフロー処理する（workflow_statusがある）コントローラで、in_progressだったらstatusをinvisibleに
		if(isset($item->workflow_status) && $item->workflow_status == 'in_progress'):
			$status = 'invisible';
		endif;

		//まず一般表示権限を確認
		$is_guest_viewable = false;
		if(
			$status != 'revision' &&
			$status != 'invisible' &&
			$deleted_at == null &&
			strtotime($created_at) <= time() &&
			strtotime($expired_at) >= time() ||
			is_null($expired_at) 
		):
			$is_guest_viewable = true;
		endif;

		if(
			\Acl\Controller_Acl::auth($controller.'/view', $userinfo) &&
			$is_guest_viewable
		):
			return $item;
		endif;

		//削除された項目を確認
		if(
			\Acl\Controller_Acl::auth($controller.'/view_deleted', $userinfo) &&
			$status != 'revision' &&
			$deleted_at != null
		):
			return $item;
		endif;

		//期限切れ項目を確認
		if(
			\Acl\Controller_Acl::auth($controller.'/view_expired', $userinfo) &&
			$status != 'revision' &&
			$deleted_at != null &&
			strtotime($expired_at) <= time()
		):
			return $item;
		endif;

		//予約項目を確認
		if(
			\Acl\Controller_Acl::auth($controller.'/view_yet', $userinfo) &&
			$status != 'revision' &&
			$deleted_at == null &&
			strtotime($created_at) >= time() &&
			strtotime($expired_at) >= time()
		):
			return $item;
		endif;

		//リビジョンを確認
		if(
			\Acl\Controller_Acl::auth($controller.'/view_revision', $userinfo) &&
			$status == 'revision'
		):
			return $item;
		endif;

		//不可視項目を確認
		if(
			\Acl\Controller_Acl::auth($controller.'/view_invisible', $userinfo) &&
			$status == 'invisible'
		):
			return $item;
		endif;

		//オーナ権限を確認（ここから）

		//コンテンツの状況を確認
		$conditions   = $is_guest_viewable               ? array('view')    : array();
		$conditions[] = $deleted_at                      ? 'view_deleted'   : '';
		$conditions[] = strtotime($created_at) >= time() ? 'view_yet'       : '';
		$conditions[] = strtotime($expired_at) <= time() ? 'view_expired'   : '';
		$conditions[] = $status == 'revision'            ? 'view_revision'  : '';
		$conditions[] = $status == 'invisible'           ? 'view_invisible' : '';

		//コントローラの情報を取得
		$request = \Request::forge();
		$current_controller = '\\'.\Request::main()->controller;
		$current_controller_obj = new $current_controller($request);
		$controller = \Inflector::denamespace($current_controller);
		$controller = strtolower(substr($controller, 11));

		//権限の確認
		$is_owner_allowed = false;
		foreach($conditions as $condition):
			if( ! $condition) continue;
			//\Acl\Controller_Acl::owner_auth()でないことに注意。このチェックはコントローラ依存する
			if(
				$current_controller_obj->owner_acl(
					$userinfo,
					$controller.'/'.$condition,
					$item
				)
			):
				$is_owner_allowed = true;
				break;
			endif;
		endforeach;
		if($is_owner_allowed) return $item;
		//オーナ権限を確認（ここまで）

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

		$orders   = is_array($orders)   ? $orders   : array($orders) ;
		$searches = is_array($searches) ? $searches : array($searches) ;
		$likes    = is_array($likes)    ? $likes    : array($likes) ;

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
			if(\DBUtil::field_exists(static::$_table_name, array('expired_at'))):
				$q->where_open();
				$q->where('expired_at', '>=', $now);
				$q->or_where('expired_at', 'is', null);
				$q->where_close();
			endif;
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

	/**
	 * delete_item()
	 *
	 * replace purge()
	 *
	 * @return object | result
	 * @author shibata@jidaikobo.com
	 */
	public static function delete_item($obj = null)
	{
		if(empty($obj)) return false;

		//deleted_atがあればsoft delete
		if(\DBUtil::field_exists(self::get_table_name(), array('deleted_at'))):
			return $obj->delete();
		endif;

		//deleted_atがなければ削除
		$primary_key = static::$_primary_key[0];
		$id = $obj->$primary_key;
		$q = \DB::delete();
		$q->table(static::$_table_name);
		$q->where($primary_key, $id);
		return $q->execute();
	}
}
