<?php
namespace Wftest;
class Controller_Wftest extends \Locomo\Controller_Crud
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 10,
		'is_for_admin' => false,
		'nicename' => 'ワークフローテスト',
		'actionset_classes' =>array(
			'base'   => '\\Wftest\\Actionset_Base_Wftest',
			'index'  => '\\Wftest\\Actionset_Index_Wftest',
			'option' => '\\Wftest\\Actionset_Option_Wftest',
		),
	);

	//trait
//	use \Locomo\Controller_Traits_Testdata;
//	use \Option\Traits_Controller_Option;
	use \Workflow\Traits_Controller_Workflow;
//	use \Revision\Traits_Controller_Revision;
}
