<?php
namespace Acl;
class Actionset_Base_Acl extends \Actionset
{
	/**
	 * actionset_controller_index()
	 */
	public static function actionset_controller_index($controller, $obj = null, $id = null, $urls = array())
	{
		$actions = array(array($controller.DS."controller_index", 'アクセス権管理'));
		$urls = static::generate_uris($controller, 'controller_index', $actions, ['view']);

		$retvals = array(
			'urls'         => $urls ,
			'action_name'  => 'アクセス権管理',
			'show_at_top'  => true,
			'explanation'  => 'ユーザグループ単位、ユーザ単位でのアクセス権設定',
			'order'        => 1,
		);
		return $retvals;
	}
}
