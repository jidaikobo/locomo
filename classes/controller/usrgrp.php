<?php
namespace Locomo;
class Controller_Usrgrp extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Revision;
	use \Controller_Traits_Bulk;

	// locomo
	public static $locomo = array(
		'nicename'     => 'ユーザグループ', // for human's name
		'explanation'  => '既存のユーザグループの名称、表示順、使用可否などを編集します。',
		'main_action'  => 'index_admin', // main action
		'main_action_name' => 'ユーザグループ管理', // main action's name
		'main_action_explanation' => '既存のユーザグループの名称、表示順、使用可否などを編集します。', // explanation of top page
		'show_at_menu' => true, // true: show at admin bar and admin/home
		'is_for_admin' => true, // true: hide from admin bar
		'order'        => 1020, // order of appearance
	);

	// model_name
	protected $model_name = '\Locomo\Model_Usrgrp';

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		\Model_Usrgrp::$_options = array(
			'where' => array(
				array('is_available', true),
				array('customgroup_uid', null),
			),
			'order_by' => array('seq' => 'ASC', 'name' => 'ASC'),
		);
		parent::index_admin();
	}

	/**
	 * action_view()
	 */
	public function action_view($id)
	{
		parent::view($id);
	}

	/**
	 * action_create()
	 */
	public function action_create()
	{
		parent::create();
	}

	/**
	 * action_edit()
	 */
	public function action_edit($id)
	{
		parent::edit($id);
	}

	/**
	 * action_bulk()
	 */
	public function action_bulk($page_num = 1)
	{
		// bulk
		\Model_Usrgrp::disable_filter();
		$option = array('where' => array(array('is_available', 'is not', null)));
		\Model_Usrgrp::$_options = array();
		$form = $this->bulk($option, '\Model_Usrgrp');

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
