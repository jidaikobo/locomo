<?php
namespace Scaffold;
class Actionset
{
	public static function actionItems()
	{
	return (object) array(
		'main' => array(
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
	);
	}
	
}
