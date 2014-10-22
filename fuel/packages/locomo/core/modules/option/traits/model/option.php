<?php
namespace Option;
trait Model_Option
{
	/**
	 * add_option()
	 */
	public static function add_option($vals = null)
	{
		if(empty($vals)) return false;

		//set
		$sets = array();
		foreach($vals as $field => $val):
			if( ! \DBUtil::field_exists(static::$_table_name, array($field))) continue;
			$sets[$field] = $val;
		endforeach;
		$sets['is_available'] = true;

		//sql
		$q = \DB::insert();
		$q->table(static::$_table_name);
		$q->set($sets);
		return $q->execute();
	}

	/**
	 * delete_option()
	 */
	public static function delete_option($id = null)
	{
		if(empty($id)) return false;
		$primary_key = reset(static::$_primary_key);

		//sql
		$q = \DB::delete();
		$q->table(static::$_table_name);
		$q->where($primary_key, $id);
		return $q->execute();
	}

	/**
	 * update_option()
	 */
	public static function update_option($vals = null)
	{
		if(empty($vals)) return false;
		$primary_key = reset(static::$_primary_key);

		//set
		$sets = array();
		foreach($vals as $field => $val):
			if( ! \DBUtil::field_exists(static::$_table_name, array($field))) continue;
			if($field == $primary_key) continue;
			$sets[$field] = $val;
		endforeach;

		//sql
		$q = \DB::update();
		$q->table(static::$_table_name);
		$q->set($sets);
		$q->where($primary_key, (int) $vals[$primary_key]);
		return $q->execute();
	}
}