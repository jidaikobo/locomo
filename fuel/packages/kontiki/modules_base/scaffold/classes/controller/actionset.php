<?php
namespace Scaffold;
class Actionset
{
	public static function actionItems()
	{
	return array(
		'main' => array(
			'is_acl'       => false,
			'url'          => '/scaffold/main',
			'action_name'  => '足場組みのメニュー',
			'explanation'  => '足場組みのメニュー。管理者のみアクセス可能。',
			'dependencies' => array(
				'main',
			)
		),
		'scaffold' => array(
			'is_acl'       => false,
			'url'          => '/scaffold/scaffold',
			'action_name'  => '足場組みフォーム',
			'explanation'  => '疑似oil書式post用のフォーム。管理者のみアクセス可能。',
			'dependencies' => array(
				'main',
				'scaffold',
			)
		),
		'migration' => array(
			'is_acl'       => false,
			'url'          => '/scaffold/migration',
			'action_name'  => 'マイグレーション用のフォーム',
			'explanation'  => 'マイグレーション用のフォーム。管理者のみアクセス可能。',
			'dependencies' => array(
				'main',
				'migration',
			)
		),
	);
	}
	
}
