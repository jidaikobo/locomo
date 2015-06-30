<?php
namespace Locomo;
class Observer_Updaterid extends \Orm\Observer
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
	public function before_save(\Orm\Model $obj)
	{
		// 通常は強制的に現在のユーザで上書きするがmigration時など、\Authが存在しない場合は-2（ルートユーザ）での更新とする
		$obj->updater_id = \Auth::get('id', -2);
	}
}
