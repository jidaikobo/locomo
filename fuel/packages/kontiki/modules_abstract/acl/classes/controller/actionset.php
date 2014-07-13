<?php
namespace Acl;
class Actionset
{
	public static function actionItems()
	{
	return (object) array(
		'controller_index' => array(
			'is_index'     => true,
			'url'          => '/acl/controller_index',
			'id_segment'   => null,
			'action_name'  => 'アクセス権限の設定',
			'menu_str'     => 'アクセス権限設定',
			'explanation'  => 'アクセス権限設定権限です。',
			'dependencies' => array(
				'controller_index',
				'actionset_index',
				'action_update_acl',
				'update_owner_acl',
			)
		),
	);
	}
	
}
