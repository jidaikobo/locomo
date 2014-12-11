<?php
namespace XXX;
class Controller_XXX extends \Locomo\Controller_Crud
{
	// locomo
	public static $locomo = array(
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order_at_menu' => 10,   // order of appearance
		'is_for_admin' => false, // true: hide from admin bar
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
	use \Locomo\Controller_Traits_Testdata;
//	use \Option\Traits_Controller_Option;
//	use \Workflow\Traits_Controller_Workflow;
//	use \Revision\Traits_Controller_Revision;
}
