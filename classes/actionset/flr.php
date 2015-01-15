<?php
namespace Locomo;
class Actionset_Flr extends \Actionset_Base
{
	/**
	 * actionset_upload()
	 */
	public static function actionset_upload($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."upload", '新規アップロード'));
		$urls = static::generate_urls($controller.DS.'create', $actions);

		$retvals = array(
			'realm'        => 'base',
			'urls'         => $urls,
			'action_name'  => '新規作成',
			'show_at_top'  => true,
			'explanation'  => '新しい項目を追加します。',
			'help'         => '新しい項目を追加します。',
			'acl_exp'      => '新規アップロード権限。',
			'order'        => 10,
			'dependencies' => array(
				$controller.DS.'view',
				$controller.DS.'upload',
			)
		);

		return $retvals;
	}
}
