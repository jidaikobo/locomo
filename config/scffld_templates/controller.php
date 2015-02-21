<?php
namespace XXX;
class Controller_XXX extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Crud;
	use \Controller_Traits_Testdata;
//	use \Controller_Traits_Wrkflw;
//	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'nicename' => '###NICENAME###', // for human's name
		'explanation' => '###NICENAME###のコントローラです', // use at admin/admin/home
		'main_action' => 'action_index_admin', // main action
		'main_action_name' => '管理一覧', // main action's name
		'main_action_explanation' => '###NICENAME###のトップです。', // explanation of top page
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order' => 10,   // order of appearance
		'is_for_admin' => false, // true: place it admin's menu instead of normal menu
		'no_acl' => false, // true: admin's action. it will not appear at acl.
		'widgets' => array(
		),
	);

	/**
	 * index_core()
	 */
	public function index_core()
	{
		parent::index_core();
		$search_form = \Model_XXX::search_form();
		$this->template->content->set_safe('search_form', $search_form);
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		// free word search
		$all = \Input::get('all') ? '%'.\Input::get('all').'%' : '' ;
		if ($all)
		{
			\Model_XXX::$_options['where'][] = array(
				array('name', 'LIKE', $all),
				'or' => array(
					array('kana', 'LIKE', $all),
					'or' => array(
						array('company_name', 'LIKE', $all), 
						'or' => array(
							array('company_kana', 'LIKE', $all),
							'or' => array(
								array('mail', 'LIKE', $all),
								'or' => array(
									array('address', 'LIKE', $all),
									'or' => array(
										array('memo', 'LIKE', $all),
									)
								)
							)
						)
					)
				) 
			);
		}

		// to controller base
		parent::index_admin();
	}
}
