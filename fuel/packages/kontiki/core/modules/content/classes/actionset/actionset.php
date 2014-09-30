<?php
namespace Content;
class Actionset_Content
{
	public static function actionItems()
	{
	return (object) array(
		//home
		'home' => array(
			'is_index'     => false,
			'url'          => '/content/home',
			'id_segment'   => null,
			'action_name'  => 'ホーム',
			'menu_str'     => 'ホーム',
			'explanation'  => 'サイトのトップです',
			'dependencies' => array(
				'home',
			)
		),
		//404
		'404' => array(
			'is_index'     => false,
			'url'          => '/content/404',
			'id_segment'   => null,
			'action_name'  => 'not found用のエラーページ',
			'menu_str'     => 'not found',
			'explanation'  => 'not found用のエラーページです',
			'dependencies' => array(
				'404',
			)
		),
		//fetch_view
		'fetch_view' => array(
			'is_index'     => false,
			'url'          => '/content/fetch_view',
			'id_segment'   => null,
			'action_name'  => 'view',
			'menu_str'     => 'アクセス権限設定',
			'explanation'  => 'アクセス権限設定権限です。',
			'dependencies' => array(
				'fetch_view',
			)
		),
	);
	}
	
}
