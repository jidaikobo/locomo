<?php
namespace XXX;
class Controller_XXX extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Testdata;
//	use \Controller_Traits_Wrkflw;
//	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'nicename' => '###nicename###', // for human's name
		'explanation' => '###nicename###のコントローラです', // use at admin/admin/home
		'main_action' => 'index_admin', // main action
		'main_action_name' => '管理一覧', // main action's name
		'main_action_explanation' => '###nicename###のトップです。', // explanation of top page
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order_at_menu' => 10,   // order of appearance
		'is_for_admin' => false, // true: place it admin's menu instead of normal menu
		'no_acl' => false, // true: admin's action. it will not appear at acl.
		'widgets' => array(
		),
	);
}
