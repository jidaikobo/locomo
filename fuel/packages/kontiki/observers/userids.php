<?php
namespace Kontiki_Observer;
class Userids extends \Orm\Observer
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
		$userinfos = \User\Controller_User::$userinfo;
		$obj->creator_id  = $obj->creator_id ? $obj->creator_id : $userinfos['user_id'];
		$obj->modifier_id = $userinfos['user_id'];
	}
}
