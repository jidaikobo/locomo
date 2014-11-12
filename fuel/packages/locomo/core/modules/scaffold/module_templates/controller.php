<?php
namespace XXX;
class Controller_XXX extends \Locomo\Controller_Crud
{
	//locomo
	public static $locomo = array(
		'nicename' => '###nicename###',
		'actionset_classes' =>array(
			'base'   => '\\XXX\\Actionset_Base_XXX',
			'index'  => '\\XXX\\Actionset_Index_XXX',
			'option' => '\\XXX\\Actionset_Option_XXX',
		),
	);

	//trait
	use \Locomo\Controller_Traits_Testdata;
//	use \Option\Traits_Controller_Option;
//	use \Workflow\Traits_Controller_Workflow;
//	use \Revision\Traits_Controller_Revision;
}
