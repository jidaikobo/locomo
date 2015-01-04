<?php
namespace Locomo;
class Actionset_Index_Usr extends \Actionset_Index
{
	/**
	 * index()
	 * user module is not for public. if you want to make this action in public, create controller for it.
	 */
	public static function actionset_index($controller, $obj = null, $id = null, $urls = array())
	{
		return array();
	}
}
