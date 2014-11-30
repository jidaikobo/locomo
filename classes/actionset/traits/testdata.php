<?php
namespace Locomo;
trait Actionset_Traits_Testdata
{
	/**
	 * actionset_add_testdata()
	 */
	public static function actionset_add_testdata($controller, $obj = null, $id = null, $urls = array())
	{
		$url = '';
		$usergroup_ids = \Auth::get_groups();

		//ルート管理者のみ
		if (in_array(-2, $usergroup_ids)):
			$url = \Html::anchor(\Inflector::ctrl_to_dir($controller).DS."add_testdata", 'テストデータの追加', array('class' => 'confirm', 'data-jslcm-msg' => 'テストデータを追加してよいですか？'));
		endif;

		//インデクスでしか表示しない
		$url = (substr(\Uri::string(), -12) == '/index_admin') ? $url : '';

		$retvals = array(
			'urls'         => array($url),
			'order'        => 10,
			'dependencies' => array(
//				'add_testdata',//ACLの対象ではない
			)
		);
		return $retvals;
	}
}
