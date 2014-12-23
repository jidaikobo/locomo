<?php
namespace Locomo;
class Controller_Usrgrp extends \Locomo\Controller_Base
{
	// locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 20,
		'is_for_admin' => true,
		'no_acl' => true,
		'admin_home' => '\\Controller_Usrgrp/index_admin',
		'admin_home_name' => 'ユーザグループ管理',
		'admin_home_explanation' => '既存のユーザグループの名称、表示順、使用可否などを編集します。',
		'nicename' => 'ユーザグループ',
		'actionset' =>array(
			'base' => array(
				array(
					'show_at_top' => true,
					'explanation' => 'ユーザグループを新規作成します。',
					'urls' => array(
						'\\Controller_Usrgrp/index_admin?create=1' => '新規作成',
					),
					'order' => 10,
				),
				array(
					'show_at_top' => true,
					'explanation' => 'ユーザグループの編集履歴です。',
					'urls' => array(
						'\\Controller_Usrgrp/index_revision' => '履歴',
					),
					'order' => 15,
				),
			),
		),
	);

	// trait
	use \Controller_Traits_Revision;
	use \Controller_Traits_Bulk;

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
		$view->base_assign();
		$this->template->content = $view;
	}
}
