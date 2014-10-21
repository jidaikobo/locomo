<?php
namespace Locomo_Core_Module\Content;
class Actionset_Content extends \Actionset
{
	public static function set_actionset($module = null, $obj = null)
	{
		static::$actions = (object) array(
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
		//403
		'403' => array(
			'is_index'     => false,
			'url'          => '',
			'id_segment'   => null,
			'action_name'  => 'forbidden用のエラーページ',
			'menu_str'     => '',
			'explanation'  => 'forbidden用のエラーページです',
			'dependencies' => array(
				'403',
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
