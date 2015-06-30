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
			'explanation'  => '新規モジュール作成の足場組み。',
			'order'        => 1,
		);
		return $retvals;
	}
}
