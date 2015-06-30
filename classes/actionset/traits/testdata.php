<?php
namespace Locomo;
trait Actionset_Traits_Testdata
{
	/**
	 * actionset_add_testdata()
	 */
	public static function actionset_add_testdata($controller, $obj = null, $id = null, $urls = array())
	{
		if (\Fuel::$env != 'development') return array();

		// only for root
		if (\Auth::is_root())
		{
			$urls = array(array($controller.DS."add_testdata", 'テストデータの追加', array('class' => 'confirm', 'data-jslcm-msg' => 'テストデータを追加してよいですか？')));
		}

		$retvals = array(
			'realm'        => 'option',
			'urls'         => $urls,
			'show_at_top'  => true,
			'explanation'  => 'ランダムな値のテストデータを10件作成します（root管理者のみ）。',
			'order'        => 10,
		);
		return $retvals;
	}
}
