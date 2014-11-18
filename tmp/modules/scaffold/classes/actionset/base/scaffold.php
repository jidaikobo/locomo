<?php
namespace Scaffold;
class Actionset_Base_Scaffold extends \Actionset
{
	/**
	 * actionset_controller_index()
	 */
	public static function actionset_controller_index($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."main", '足場組み'));
		$urls = static::generate_uris($controller, 'main', $actions);

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
