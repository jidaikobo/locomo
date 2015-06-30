<?php
namespace Locomo;
class Observer_Creatorid extends \Orm\Observer
{
	/**
	 * __construct()
	 */
	public function __construct($class)
	{
	}

	/**
	 * before_insert()
	 */
	public function before_insert(\Orm\Model $obj)
	{
		// 指定がない場合は-2（ルートユーザ）
		$obj->creator_id = \Auth::get('id', -2);
	}
}
