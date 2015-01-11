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

	/**
	 * action_index_admin()
	 */
	public function action_index_admin($page_num = 1)
	{
		// bulk
		\Model_Usrgrp::disable_filter();
		$option = array('where' => array(array('is_available', 'is not', null)));
		\Model_Usrgrp::$_conditions = array();
		$form = $this->bulk($option, array(), '\Model_Usrgrp');

		// add_actionset - back to index at edit
		$ctrl_url = \Inflector::ctrl_to_dir(\Request::main()->controller);
		$action['urls'][] = \Html::anchor($ctrl_url.DS.'index_admin/','一覧へ');
		$action['order'] = 10;
		\Actionset::add_actionset(\Request::main()->controller, 'ctrl', $action);

		// assign
		$view = \View::forge('bulk/bulk');
		$view->set_global('title', 'ユーザグループ設定');
		$view->set_global('form', $form, false);
		$this->base_assign();
		$this->template->content = $view;
	}
}
