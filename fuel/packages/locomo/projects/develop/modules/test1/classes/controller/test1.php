<?php
namespace Test1;
class Controller_Test1 extends \Locomo\Controller_Crud
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 10,
		'is_for_admin' => false,
		'nicename' => 'テスト1',
		'actionset_classes' =>array(
			'base'   => '\\Test1\\Actionset_Base_Test1',
			'index'  => '\\Test1\\Actionset_Index_Test1',
			'option' => '\\Test1\\Actionset_Option_Test1',
		),
	);

	//trait
	use \Locomo\Controller_Traits_Testdata;
//	use \Option\Traits_Controller_Option;
//	use \Workflow\Traits_Controller_Workflow;
//	use \Revision\Traits_Controller_Revision;
}
