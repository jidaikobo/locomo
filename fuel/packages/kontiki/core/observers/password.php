<?php
namespace Kontiki\Observer;
class Password extends \Orm\Observer
{
	/**
	 * @var  string  default setting, md5 or sha1?
	 */
//	public static $method = 'md5';

	/**
	 * @var  string  property to set hash function
	 */
//	public static $_method;

	/**
	 * @var  string  default property to set the password on
	 */
	public static $property = 'password';

	/**
	 * @var  string  property to set the password on
	 */
	protected $_property;

	/**
	 * Set the properties for this observer instance, based on the parent model's
	 * configuration or the defined defaults.
	 *
	 * @param  string  Model class this observer is called on
	 */
	public function __construct($class)
	{
		$props = $class::observers(get_class($this));
		$this->_property = isset($props['property']) ? $props['property'] : static::$property;
//		$this->_method = isset($props['method']) ? $props['method'] : static::$method;
	}

	/**
	 * Set the password property to hashed strings when insertion.
	 *
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_insert(\Orm\Model $obj)
	{
		$this->before_save($obj);
	}

	/**
	 * Set the password property to hashed strings when \Input::post($this->_property) given.
	 *
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_save(\Orm\Model $obj)
	{
		$property = \Input::post($this->_property);
		$property = $property ? $property : @$obj->{$this->_property};

		if ( ! empty($property)):
			$obj->{$this->_property} = md5($property);
		else:
			//$objをvar_dump()すると、_originalが見えるので、きっとここ、リファクタリングできる
			$originals = $obj->get_original_values();
			$obj->{$this->_property} = $originals[$this->_property];
		endif;
	}
}
