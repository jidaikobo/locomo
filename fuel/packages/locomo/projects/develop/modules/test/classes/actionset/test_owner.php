<?php
namespace Test;
class Actionset_Owner_Test extends \Actionset_Owner
{
	/**
	 * set_actionset()
	 */
	public static function set_actionset()
	{
		static::$actions = (object) array();
//		parent::actionItems();
	}
}
