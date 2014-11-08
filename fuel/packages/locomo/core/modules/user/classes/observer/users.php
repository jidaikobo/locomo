<?php
namespace User;
class Observer_Users extends \Orm\Observer
{
	/**
	 * __construct()
	 */
	public function __construct($class)
	{
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
		$obj->last_login_at   = $obj->last_login_at ? $obj->last_login_at : '';
		$obj->login_hash      = $obj->login_hash ? $obj->login_hash : '';
		$obj->profile_fields  = $obj->profile_fields ? $obj->profile_fields : serialize(array());
	}
}
