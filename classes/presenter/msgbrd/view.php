<?php
namespace Locomo;
class Presenter_Msgbrd_View extends \Presenter_Base
{
	public static function parents($item, $limit = true)
	{
		if (
			!$item ||
			$limit === 0
		)
		{
			return;
		}
		elseif ($limit > 0)
		{
			$limit--;
		}


		$view = \View::forge('msgbrd/view/parents', array('item' => $item));

		if ($item->parent)
		{
			$view .= static::parents($item->parent, $limit);
		}

		return $view;
	}
}
