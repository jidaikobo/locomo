<?php
namespace Locomo;
class Inflector extends \Fuel\Core\Inflector
{
	/**
	 * to_dir()
	 */
	public static function to_dir($controller = null)
	{
		if( ! $controller) throw new \InvalidArgumentException('argument must not be null or empty');
		return str_replace('_', '/', substr(strtolower(\Inflector::denamespace($controller)), 11));
	}
}
