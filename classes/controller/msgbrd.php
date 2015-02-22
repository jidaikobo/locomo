<?php
class Controller_Msgbrd extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Bulk;
	use \Controller_Traits_Crud;
//	use \Controller_Traits_Testdata;
//	use \Controller_Traits_Wrkflw;
//	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'nicename' => 'メッセージボード', // for human's name
		'explanation' => 'メッセージボードのコントローラです', // use at admin/admin/home
		'main_action' => 'action_index_admin', // main action
		'main_action_name' => '管理一覧', // main action's name
		'main_action_explanation' => 'メッセージボードのトップです。', // explanation of top page
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order' => 950,   // order of appearance
		'is_for_admin' => false, // true: place it admin's menu instead of normal menu
		'no_acl' => false, // true: admin's action. it will not appear at acl.
		'widgets' => array(
			array('name' => 'メッセージボードより', 'uri' => '\\Controller_Msgbrd::action_index_dashboard'),
		),
	);

	/**
	 * index_core()
	 */
	public function index_core()
	{
		parent::index_core();
		$search_form = \Model_Msgbrd::search_form();
		$this->template->content->set_safe('search_form', $search_form);
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		// 下書きを除外
		if (\Request::main()->action !== 'index_draft')
		{
			\Model_Msgbrd::$_options['where'][] = array(
				array('is_draft', '=', 0)
			);
		}

		// free word search
		$all = \Input::get('all') ? '%'.\Input::get('all').'%' : '' ;
		if ($all)
		{
			\Model_Msgbrd::$_options['where'][] = array(
				array('name', 'LIKE', $all),
				'or' => array(
					array('content', 'LIKE', $all),
				)
			);
		}

		// to controller base
		parent::index_admin();
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_draft()
	{
		\Model_Msgbrd::$_options['where'][] = array(
			array('is_draft', '=', 1)
		);

		$this->action_index_admin();
	}

	/**
	 * action_edit_categories()
	 */
	public function action_edit_categories()
	{
		// bulk
		\Model_Msgbrd_Categories::disable_filter();
		$option = array(
			'where' => array(array('is_available', 'is not', null)),
			'order_by' => array('seq' => 'ASC', 'name' => 'ASC'),
		);
		\Model_Msgbrd_Categories::$_options = array();
		$form = $this->bulk($option, '\Model_Msgbrd_Categories');

		// add_actionset - back to index at edit
		$action['urls'][] = \Html::anchor(static::$main_url, '一覧へ');
		\Actionset::add_actionset(static::$controller, 'ctrl', $action);

		// assign
		$view = \View::forge('bulk/bulk');
		$view->set_global('title', 'カテゴリ設定');
		$view->set_global('form', $form, false);
		$this->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_index_dashboard()
	 */
	public function action_index_dashboard()
	{
		\Model_Msgbrd::$_options['where'][] = array(
			array('is_sticky', '=', 1)
		);
		$this->action_index_admin();
	}
}
