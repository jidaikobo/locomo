<?php
namespace Kontiki;

abstract class Model_Meta_Abstract extends \Orm\Model
{
	protected static $_primary_key = array('controller','controller_id','meta_key');

	protected static $_table_name = 'meta';

	protected static $_properties = array(
		'controller',
		'controller_id',
		'meta_key',
		'meta_value',
	);

	protected static $_observers = array(
	);

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
}
