<?php
class Controller_Adrs extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Crud;
	use \Controller_Traits_Bulk;
//	use \Controller_Traits_Testdata;
//	use \Controller_Traits_Wrkflw;
//	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'nicename' => 'アドレス帳', // for human's name
		'explanation' => 'アドレス帳のコントローラです', // use at admin/admin/home
		'main_action' => 'index_admin', // main action
		'main_action_name' => '管理一覧', // main action's name
		'main_action_explanation' => 'アドレス帳のトップです。', // explanation of top page
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order' => 1000,   // order of appearance
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
		$search_form = \Model_Adrs::search_form();
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
			\Model_Adrs::$_options['where'][] = array(
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

		// group
		$group = \Input::get('group', null) ;
		if ($group)
		{
			\Model_Adrs::$_options['where'][] = array(
				array('group_id', '=', $group),
			);
		}

		// to controller base
		parent::index_admin();
	}

	/**
	 * action_edit_adrsgrp()
	 */
	public function action_edit_adrsgrp()
	{
		// bulk
		\Model_Adrsgrp::disable_filter();
		$option = array(
			'where' => array(array('is_available', 'is not', null)),
			'order_by' => array('seq' => 'ASC'),
		);
		\Model_Adrsgrp::$_options = array();
		$form = $this->bulk($option, '\Model_Adrsgrp');

		// add_actionset - back to index at edit
		$action['urls'][] = \Html::anchor(static::$main_url, '一覧へ');
		\Actionset::add_actionset(static::$controller, 'ctrl', $action);

		// assign
		$view = \View::forge('bulk/bulk');
		$view->set_global('title', 'ユーザグループ設定');
		$view->set_global('form', $form, false);
		$this->base_assign();
		$this->template->content = $view;
	}
}
