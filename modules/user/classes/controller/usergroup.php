<?php
namespace User;
class Controller_Usergroup extends \Locomo\Controller_Base
{
	// locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 20,
		'is_for_admin' => false,
		'admin_home' => '\\User\\Controller_Usergroup/index_admin',
		'admin_home_name' => 'ユーザグループ管理',
		'admin_home_explanation' => '既存のユーザグループの名称、表示順、使用可否などを編集します。', // explanation of module's top page
		'nicename' => 'ユーザグループ',
		'help'     => 'packages/locomo/modules/user/help/user.html',//app/../からのパス
		'actionset' =>array(
			'base' => array(
				array(
					'show_at_top' => true,
					'explanation' => 'ユーザグループを新規作成します。',
					'urls' => array(
						'\\User\\Controller_Usergroup/index_admin?create=1' => '新規作成',
					),
					'order' => 10,
				),
				array(
					'show_at_top' => true,
					'explanation' => 'ユーザグループの編集履歴です。',
					'urls' => array(
						'\\User\\Controller_Usergroup/index_revision' => '履歴',
					),
					'order' => 15,
				),
			),
		),
	);

	// trait
	use \Revision\Traits_Controller_Revision;
	use \Bulk\Traits_Controller_Bulk;

	/**
	 * action_index_admin()
	 */
	public function action_index_admin($page_num = 1)
	{
		// bulk
		\User\Model_Usergroup::disable_filter();
		$form = $this->bulk(array(), array(), '\User\Model_Usergroup');

		//assign
		$view = \View::forge(LOCOMOPATH.'modules/bulk/views/bulk.php');
		$view->set_global('title', 'ユーザグループ設定');
		$view->set_global('form', $form, false);
		$view->base_assign();
		$this->template->content = $view;
	}
}
