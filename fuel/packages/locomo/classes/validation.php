<?php
namespace Locomo;
class Validation extends \Fuel\Core\Validation
{
	/**
	 * _validation_require_once()
	 * require at first time and at changing
	 * 
	 * @param str $val
	 * @param str $options perioded value
	 * 
	 * @return bool
	 * @author shibata@jidaikobo.com
	 */
	public static function _validation_require_once($val, $options)
	{
		//validate
		list($table, $field, $id) = explode('.', $options);

		//if data exists allow empty
		if($id):
			$result = \DB::select($field)
				->from($table)
				->where('id',$id)
				->execute();
			return ($result->count() > 0);
		else:
			//empty is not allowed
			return ! empty($val);
		endif;
	}

	/**
	 * _validation_unique()
	 * 
	 * @param str $val
	 * @param str $options perioded value
	 * 
	 * @return bool
	 * @author shibata@jidaikobo.com
	 */
	public static function _validation_unique($val, $options)
	{
		//validate
		list($table, $field, $id) = explode('.', $options);

		//if it is updating then allow same id
		if($id):
			$result = \DB::select("id")
			->where('id', '<>', $id)
			->where($field, '=', \Str::lower($val))
			->from($table)->execute();
			return ($result->count() == 0);
		else:
			//create
			$result = \DB::select("id")
			->where($field, '=', \Str::lower($val))
			->from($table)->execute();
			return ! ($result->count() > 0);
		endif;
	}

	/**
	 * _validation_match_password()
	 * 
	 * @param str $val
	 * @param str $options perioded value
	 * 
	 * @return bool
	 * @author shibata@jidaikobo.com
	 */
	public static function _validation_match_password($val, $options)
	{
		//validate
		list($table, $field, $id) = explode('.', $options);
		if($id):
			$result = \DB::select("id")
			->where('id', '=', $id)
			->where($field, '=', \Auth::instance()->hash_password($val))
			->from($table)->execute();
			return ($result->count() >= 1);
		endif;
	}

	/**
	 * _validation_match_db_field()
	 * 
	 * @param str $val
	 * @param str $options perioded value
	 * 
	 * @return bool
	 * @author shibata@jidaikobo.com
	 */
	public static function _validation_match_db_field($val, $options)
	{
		//validate
		list($table, $field, $id) = explode('.', $options);
		if($id):
			$result = \DB::select("id")
			->where('id', '=', $id)
			->where($field, '=', $val)
			->from($table)->execute();
			return ($result->count() >= 1);
		endif;
	}

	/**
	 * _validation_banned_string()
	 * 
	 * @param str $val
	 * @param str $options perioded value
	 * 
	 * @return bool
	 * @author shibata@jidaikobo.com
	 */
	public static function _validation_banned_string($val, $options)
	{
		return ! in_array($val, $options);
	}
}
