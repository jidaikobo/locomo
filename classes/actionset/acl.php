<?php
namespace Locomo;
class Actionset_Acl extends \Actionset
{
	/**
	 * actionset_controller_index()
	 */
	public static function actionset_controller_index($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => array(
				array("\Controller_Acl/controller_index", 'アクセス権管理'),
			) ,
			'action_name'  => 'アクセス権管理',
			'explanation'  => 'ユーザやユーザグループにコントローラへのアクセス権を付与します。',
			'show_at_top'  => true,
			'order'        => 1,
		);
		return $retvals;
	}

	/**
	 * actionset_actionset_index()
	 */
	public static function actionset_actionset_index($controller, $obj = null, $id = null, $urls = array())
	{
		$retvals = array(
			'urls'         => $urls ,
			'show_at_top'  => false,
			'action_name'  => 'アクセス権管理',
			'order'        => 1,
		);
		return $retvals;
	}
}
