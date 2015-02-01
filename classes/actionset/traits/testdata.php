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

		$url = '';
		$usergroup_ids = \Auth::get_groups();

		// only for root
		if (in_array(-2, $usergroup_ids)):
			$url = \Html::anchor(\Inflector::ctrl_to_dir($controller).DS."add_testdata", 'テストデータの追加', array('class' => 'confirm', 'data-jslcm-msg' => 'テストデータを追加してよいですか？'));
		endif;

		// only for index
//		$url = (substr(\Uri::string(), -12) == '/index_admin') ? $url : '';

		$retvals = array(
			'realm'        => 'option',
			'urls'         => array($url),
			'show_at_top'  => true,
			'explanation'  => 'ランダムな値のテストデータを10件作成します（root管理者のみ）。',
			'order'        => 10,
		);
		return $retvals;
	}
}
