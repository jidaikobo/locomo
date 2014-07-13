<?php
namespace Scaffold;
class Actionset
{
	public static function actionItems()
	{
	return (object) array(
		'main' => array(
			'is_acl'       => false,
			'is_index'     => true,
			'url'          => '/scaffold/main',
			'id_segment'   => null,
			'action_name'  => '足場組みのフォーム',
			'menu_str'     => '足場組みのフォーム',
			'explanation'  => '疑似oil書式post用のフォーム。管理者のみアクセス可能。',
			'dependencies' => array(
				'main',
			)
		),
		'migration' => array(
			'is_acl'       => false,
			'is_index'     => true,
			'url'          => '/scaffold/migration',
			'id_segment'   => null,
			'action_name'  => 'マイグレーション用のフォーム',
			'menu_str'     => 'マイグレーション用のフォーム',
			'explanation'  => 'マイグレーション用のフォーム。管理者のみアクセス可能。',
			'dependencies' => array(
				'main',
				'migration',
			)
		),
	);
	}
	
}
