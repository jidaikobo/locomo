<?php
namespace Acl;
class Actionset_Base_Acl extends \Actionset
{
	public static function actionset_controller_index($module, $obj, $get_authed_url)
	{
		return array(
			'url'          => '/acl/controller_index',
			'action_name'  => 'アクセス権限の設定',
			'menu_str'     => 'アクセス権限設定',
			'explanation'  => 'アクセス権限設定権限です。',
			'dependencies' => array(
				'controller_index',
				'actionset_index',
				'actionset_owner_index',
				'update_acl',
				'update_owner_acl',
			)
		);
	}

}
