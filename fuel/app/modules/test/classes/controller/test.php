<?php
namespace Test;
class Controller_Test extends \Locomo\Controller_Crud
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order_at_menu' => 10,   // order of appearance
		'is_for_admin' => false, // true: hide from admin bar
		'admin_home' => '\\Test\\Controller_Test/admin_index', // module's top page
		'nicename' => 'テストモジュール', // for human's name
		'actionset_classes' =>array(
			'base'   => '\\Test\\Actionset_Base_Test',
			'index'  => '\\Test\\Actionset_Index_Test',
			'option' => '\\Test\\Actionset_Option_Test',
		),
	);

	//trait
	use \Locomo\Controller_Traits_Testdata;
//	use \Option\Traits_Controller_Option;
//	use \Workflow\Traits_Controller_Workflow;
//	use \Revision\Traits_Controller_Revision;
}
