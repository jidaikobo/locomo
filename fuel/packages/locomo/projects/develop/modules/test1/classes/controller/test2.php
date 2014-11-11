<?php
namespace Test1;
class Controller_Test2 extends \Locomo\Controller_Crud
{
	//trait
	use \Locomo\Controller_Traits_Testdata;
//	use \Option\Traits_Controller_Option;
//	use \Workflow\Traits_Controller_Workflow;
//	use \Revision\Traits_Controller_Revision;

	//locomo
	public static $locomo = array(
		'nicename' => 'テスト2',
		'actionset_classes' =>array(
			'base'   => '\\Test1\\Actionset_Base_Test2',
			'index'  => '\\Test1\\Actionset_Index_Test1',
			'option' => '\\Test1\\Actionset_Option_Test1',
		),
	);
}
