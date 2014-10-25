<?php
namespace Locomo;
class Expired extends \Orm\Observer
{
	/**
	 * @var  string  default property
	 */
	public static $properties = array('expired_at');

	/**
	 * @var  string  property
	 */
	protected $_properties;

	/**
	 * @var  string  default property of expired field
	 */
	public static $expired_filed = 'expired_at';

	/**
	 * @var  string  default property of expired field
	 */
	protected $_expired_filed;

	/**
	 * Set the properties for this observer instance, based on the parent model's
	 * configuration or the defined defaults.
	 *
	 * @param  string  Model class this observer is called on
	 */
	public function __construct($class)
	{
		$props = $class::observers(get_class($this));
		$this->_properties = isset($props['properties']) ? $props['properties'] : static::$properties;
		$this->_expired_filed = isset($props['expired_filed']) ? $props['expired_filed'] : static::$expired_filed;
	}

	/**
	 * before_insert()
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_insert(\Orm\Model $obj)
	{
		$this->before_save($obj);
	}

	/**
	 * before_save()
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_save(\Orm\Model $obj)
	{
		//unixtimeの上限
//		$end_of_unixtime = date('Y-m-d H:i:s', 2147483647);
//		$maxdate = '9999-12-31 23:59:59';

		//日付の形式に
		foreach($this->_properties as $property):
			$value = \Input::post($property);
			if ( ! empty($value)):
				$value = strtotime($value);
				$obj->{$property} = date('Y-m-d H:i:s', $value);
//				$obj->{$property} = $value >= 253402268398 ? $maxdate : date('Y-m-d H:i:s', $value);
			endif;
		endforeach;

		//期限切れは特別扱い。空の場合は、unixtimeの上限を入れる
		if(in_array($this->_expired_filed, $this->_properties)):
			if (empty($obj->{$property})):
				$obj->expired_at = null ;
	//			$obj->expired_at = \Input::post($this->_expired_filed) ?: $maxdate ;
			endif;
		endif;
	}
}
