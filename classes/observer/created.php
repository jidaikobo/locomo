<?php
namespace Locomo;
class Observer_Created extends \Orm\Observer
{
	/**
	 * @var  bool  default setting, true to use mySQL timestamp instead of UNIX timestamp
	 */
	public static $mysql_timestamp = false;

	/**
	 * @var  string  default property of created field
	 */
	public static $property = 'created_at';

	/**
	 * @var  bool  true to use mySQL timestamp instead of UNIX timestamp
	 */
	protected $_mysql_timestamp;

	/**
	 * @var  string  property to set the timestamp on
	 */
	protected $_property;

	/**
	 * @var  string  whether to overwrite an already set timestamp
	 */
	protected $_overwrite;

	/**
	 * Set the properties for this observer instance, based on the parent model's
	 * configuration or the defined defaults.
	 *
	 * @param  string  Model class this observer is called on
	 */
	public function __construct($class)
	{
		$props = $class::observers(get_class($this));
		$this->_mysql_timestamp  = isset($props['mysql_timestamp']) ? $props['mysql_timestamp'] : static::$mysql_timestamp;
		$this->_property         = isset($props['property']) ? $props['property'] : static::$property;
		$this->_overwrite        = isset($props['overwrite']) ? $props['overwrite'] : true;
	}

	/**
	 * before_insert()
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_insert(\Orm\Model $obj)
	{
		$utime = strtotime(\Input::post($this->_property));

		//empty means today
		if(empty($utime) || ! $utime)
		{
			$this->before_save($obj);
			return;
		}

		if($utime >= 0)
		{
			$obj->{$this->_property} = $this->_mysql_timestamp ? date('Y-m-d H:i:s', $utime) : $utime ;
			return;
		}
	}

	/**
	 * before_save()
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_save(\Orm\Model $obj)
	{
		//empty means today
		if ($this->_overwrite or empty($obj->{$this->_property}))
		{
			$obj->{$this->_property} = $this->_mysql_timestamp ? \Date::time()->format('mysql') : \Date::time()->get_timestamp();
		}
	}
}
