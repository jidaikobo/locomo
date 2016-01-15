<?php
namespace Locomo;
class Actionset_Sys extends \Actionset
{
	/**
	 * actionset_dashboard()
	 */
	public static function actionset_dashboard($controller, $obj = null, $id = null, $urls = array())
	{
		// ダッシュボードの仕様が安定するまでrootのみにする
		if (\Auth::is_root())
		{
			$urls = array(array($controller.DS."edit/".\Auth::get('id'), 'ダッシュボード編集'));
		}

		// ダッシュボードでのみ表示
		if (\Request::main()->action == 'dashboard')
		{
			$urls = array(array($controller.DS."edit/".\Auth::get('id'), 'ダッシュボード編集'));
		} else {
			$urls = array();
		}

		$retvals = array(
			'realm'        => 'option',
			'urls'         => $urls,
			'order'        => 10,
		);

		return $retvals;
	}
}
