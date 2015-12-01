<?php
namespace Locomo;
class Actionset_Scffld extends \Actionset
{
	/**
	 * actionset_controller_index()
	 */
	public static function actionset_controller_index($controller, $obj = null, $id = null, $urls = array())
	{
		$urls = array(array($controller.DS."main", '足場組み'));

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '足場組み',
			'show_at_top'  => true,
			'explanation'  => '足場組み',
			'order'        => 1,
		);
		return $retvals;
	}

	/**
	 * actionset_controller_destroy()
	 */
	public static function actionset_controller_destroy($controller, $obj = null, $id = null, $urls = array())
	{
		$urls = array(array($controller.DS."destory", '削除'));

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => '削除',
			'show_at_top'  => true,
			'explanation'  => '削除',
			'order'        => 10,
		);
		return $retvals;
	}
}
