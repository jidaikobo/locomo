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
	 */
	public static function _validation_require_once($val, $options)
	{
		//validate
		list($table, $field, $id) = explode('.', $options);

		//if data exists allow empty
		if ($id)
		{
			$result = \DB::select($field)
				->from($table)
				->where('id', $id)
				->execute();
			return ($result->count() > 0);
		}
		else
		{
			//empty is not allowed
			return ! empty($val);
		}
	}

	/**
	 * _validation_unique()
	 *
	 * @param str $val
	 * @param str $options perioded value
	 *
	 * @return bool
	 */
	public function _validation_unique($val, $options)
	{
		//validate
		list($table, $field) = explode('.', $options);
		$id = $this->callables[0]->id;

		//if it is updating then allow same id
		if ($id)
		{
			$result = \DB::select("id")
			->where('id', '<>', $id)
			->where($field, '=', \Str::lower($val))
			->from($table)->execute();
			return ($result->count() == 0);
		}
		else
		{
			//create
			$result = \DB::select("id")
			->where($field, '=', \Str::lower($val))
			->from($table)->execute();
			return ! ($result->count() > 0);
		}
	}

	public function ___validation_unique($val, $options)
	{
		//validate
		list($table, $field, $id) = explode('.', $options);

//$this->callables[0]->id

		//if it is updating then allow same id
		if ($id)
		{
			$result = \DB::select("id")
			->where('id', '<>', $id)
			->where($field, '=', \Str::lower($val))
			->from($table)->execute();
			return ($result->count() == 0);
		}
		else
		{
			//create
			$result = \DB::select("id")
			->where($field, '=', \Str::lower($val))
			->from($table)->execute();
			return ! ($result->count() > 0);
		}
	}


	/**
	 * _validation_match_password()
	 *
	 * @param str $val
	 * @param str $options perioded value
	 *
	 * @return bool
	 */
	public static function _validation_match_password($val, $options)
	{
		//validate
		list($table, $field, $id) = explode('.', $options);
		if ($id)
		{
			$result = \DB::select("id")
			->where('id', '=', $id)
			->where($field, '=', \Auth::instance()->hash_password($val))
			->from($table)->execute();
			return ($result->count() >= 1);
		}
	}

	/**
	 * _validation_match_db_field()
	 *
	 * @param str $val
	 * @param str $options perioded value
	 *
	 * @return bool
	 */
	public static function _validation_match_db_field($val, $options)
	{
		//validate
		list($table, $field, $id) = explode('.', $options);
		if ($id)
		{
			$result = \DB::select("id")
			->where('id', '=', $id)
			->where($field, '=', $val)
			->from($table)->execute();
			return ($result->count() >= 1);
		}
	}

	/**
	 * _validation_banned_string()
	 *
	 * @param str $val
	 * @param str $options perioded value
	 *
	 * @return bool
	 */
	public static function _validation_banned_string($val, $options)
	{
		return ! in_array($val, $options);
	}

	/**
	 * _validation_non_zero_datetime()
	 * require at first time and at changing
	 *
	 * @param str $val
	 * @param str $options
	 *
	 * @return bool
	 */
	public static function _validation_non_zero_datetime($val)
	{
		if (empty($val)) return true; // empty string is today.
		if ( ! strtotime($val) || strtotime($val) <= 0) return false;
		return true;
	}


	public function _validation_required_least($val, $fields = array(), $least = 1)
	{
		$inputed = 0;

		foreach ($fields as $field)
		{
			if ($this->input($field) !== '') $inputed++;
		}


		if($least > $inputed)
		{
			$labels = array();
			foreach ($fields as $field)
			{
				$labels[] = $this->field($field)->label;
			}
			throw new \Validation_Error($this->active_field(), $val, array('required_least' => array($this->field($field))), array(implode(',', $labels)));
		}

		return true;
	}
}
