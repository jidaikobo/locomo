<?php
namespace Kontiki;
abstract class Model_Option extends \Kontiki\Model_Crud
{
	protected static $_table_name = 'options';

	/**
	 * add_option()
	 */
	public static function add_option($table = null, $vals = null)
	{
		if(empty($table) || empty($vals)) return false;

		//set
		$sets = array();
		foreach($vals as $field => $val):
			if( ! \DBUtil::field_exists($table, array($field))) continue;
			$sets[$field] = $val;
		endforeach;
		$sets['is_available'] = true;

		//sql
		$q = \DB::insert();
		$q->table($table);
		$q->set($sets);
		return $q->execute();
	}

	/**
	 * delete_option()
	 */
	public static function delete_option($table = null, $id = null)
	{
		if(empty($table) || empty($id)) return false;

		//sql
		$q = \DB::delete();
		$q->table($table);
		$q->where('id', $id);
		return $q->execute();
	}

	/**
	 * update_option()
	 */
	public static function update_option($table = null, $vals = null)
	{
		if(empty($table) || empty($vals)) return false;

		//set
		$sets = array();
		foreach($vals as $field => $val):
			if( ! \DBUtil::field_exists($table, array($field))) continue;
			if($field == 'id') continue;
			$sets[$field] = $val;
		endforeach;

		//sql
		$q = \DB::update();
		$q->table($table);
		$q->set($sets);
		$q->where('id', (int) $vals['id']);
		return $q->execute();
	}

	/**
	 * find_options()
	 */
	public static function find_options($optname = null)
	{
		if(empty($optname)) return false;

		//独自のテーブルを持っている場合はそちらを使う。
		//その場合はoptnameはtable名を指定すること
		$table = \DBUtil::table_exists($optname) ? $optname : 'options' ;

		//sql
		$q = \DB::select('*');
		$q->from($table);
		$q->order_by('order', 'ASC');
		$items = $q->as_object()->execute()->as_array();

		//たぶん\Viewがちょっとおかしくて、配列が二重になるので、ここで対応
		$retvals = (object) array();
		foreach($items as $k => $item):
			$retvals->$k = (object) $item;
		endforeach;

		return $retvals;
	}

	/**
	 * get_options()
	 */
	public static function get_options($table = null, $is_option = true)
	{
		if(empty($table)) return false;

		//sql
		$q = \DB::select('*');
		$q->from($table);
		$q->where('is_available', true);
		$q->order_by('order', 'ASC');
		$items = $q->execute()->as_array();

		if($is_option):
			return \Arr::assoc_to_keyval($items, 'id', 'name');
		else:
			return $items;
		endif;
	}

	/**
	 * update_options_relations()
	 */
	public static function update_options_relations($optname = null, $id = null)
	{
		if(empty($optname) || empty($id)) return false;
		$table = $optname.'_r';

		//clean up
		$q = \DB::delete();
		$q->table($table);
		$q->where('item_id', $id);
		$q->execute();

		//return
		$options = \Input::post($optname);
		if(empty($options)) return;

		//配列の場合（チェックボックスやmultipleのselectなど）
		if(is_array($options)):
			foreach($options as $option):
				$q = \DB::insert();
				$q->table($table);
				$q->set(array('item_id' => $id, 'option_id' => $option));
				$q->execute();
			endforeach;
		//配列でない場合（radioなど）
		else:
			$q = \DB::insert();
			$q->table($table);
			$q->set(array('item_id' => $id, 'option_id' => $options));
			$q->execute();
		endif;

		return ;
	}

	/**
	 * get_selected_options()
	 */
	public static function get_selected_options($optname = null, $id = true)
	{
		if(empty($optname) || empty($id)) return false;
		$table = $optname.'_r';

		$q = \DB::select('option_id');
		$q->from($table);
		$q->where('item_id', $id);
		return \Arr::flatten($q->execute()->as_array()) ?: array() ;
	}
}