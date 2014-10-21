<?php
namespace XXX;
class Actionset_Owner_XXX extends \Actionset_Owner
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
