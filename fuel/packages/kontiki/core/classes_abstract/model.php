<?php
namespace Kontiki;
abstract class Model_Abstract extends \Orm\Model_Soft
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
}
