<?php
namespace XXX;
class Controller_XXX extends \Locomo\Controller_Base
{
	// locomo
	public static $locomo = array(
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order_at_menu' => 10,   // order of appearance
		'is_for_admin' => false, // true: place it admin's menu instead of normal menu
		'no_acl' => false, // true: admin's action. it will not appear at acl.
		'admin_home' => '\\XXX\\Controller_XXX/index_admin', // module's top page
		'admin_home_name' => '管理一覧', // name of module's top page
		'admin_home_explanation' => '###nicename###のトップです。', // explanation of module's top page
		'nicename' => '###nicename###', // for human's name
		'explanation' => '###nicename###のコントローラです', // use at admin/admin/home
		'help'     => 'packages/locomo/modules/user/help/user.html',// path from 'app/../'

		// actionset_classes
		'actionset_classes' =>array(
			'base'   => '\\XXX\\Actionset_Base_XXX',
			'index'  => '\\XXX\\Actionset_Index_XXX',
			'option' => '\\XXX\\Actionset_Option_XXX',
		),
/*
		// actionset_methods
		'actionset_methods' =>array(
			'base'   => array(
				'actionset_SOMETHING_EXISTS_IN_THIS_CLASS',
			),
		),
		// actionset
		'actionset' =>array(
			'base' => array(
				array(
					'show_at_top' => true,
					'explanation' => '###nicename###',
					'urls' => array(
						'\\XXX\\Controller_XXX/index_admin' => '###nicename###',
					),
					'order' => 10,
				),
			),
		),
*/
	);

	// trait
	use \Controller_Traits_Testdata;
//	use \Controller_Traits_Wrkflw;
//	use \Controller_Traits_Revision;
}
