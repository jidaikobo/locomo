<?php
namespace Locomo;
class Actionset_Sys extends \Actionset
{
	/**
	 * actionset_dashboard()
	 */
	public static function actionset_dashboard($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."edit/".\Auth::get('id'), 'ダッシュボード編集'));
		$urls = static::generate_urls($controller.DS.'create', $actions);

		$retvals = array(
			'realm'        => 'option',
			'urls'         => $urls,
			'order'        => 10,
		);

		return $retvals;
	}
}