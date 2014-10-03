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
			'url'          => '',
			'id_segment'   => null,
			'action_name'  => 'ホーム',
			'menu_str'     => '',
			'explanation'  => 'サイトのトップです',
			'dependencies' => array(
				'home',
			)
		),
		//404
		'404' => array(
			'is_index'     => false,
			'url'          => '',
			'id_segment'   => null,
			'action_name'  => 'not found用のエラーページ',
			'menu_str'     => '',
			'explanation'  => 'not found用のエラーページです',
			'dependencies' => array(
				'404',
			)
		),
		//fetch_view
		'fetch_view' => array(
			'is_index'     => false,
			'url'          => '',
			'id_segment'   => null,
			'action_name'  => 'パイプ用アクション',
			'menu_str'     => '',
			'explanation'  => 'viewディレクトリからcoreディレクトリ内にアクセスするためのアクションです',
			'dependencies' => array(
				'fetch_view',
			)
		),
	);
	}
	
}
