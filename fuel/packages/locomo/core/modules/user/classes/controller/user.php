<?php
namespace User;
class Controller_User extends \Locomo\Controller_Crud
{
	//locomo
	public static $locomo = array(
		'show_at_menu' => true,
		'order_at_menu' => 10,
		'is_for_admin' => true,
		'nicename' => 'ユーザ',
		'actionset_classes' =>array(
			'base'   => '\\User\\Actionset_Base_User',
			'index'  => '\\User\\Actionset_Index_User',
			'option' => '\\User\\Actionset_Option_User',
		),
	);

	//trait
	use \Locomo\Controller_Traits_Testdata;
	use \Revision\Traits_Controller_Revision;
	use \Bulk\Traits_Controller_Bulk;

	/**
	 * action_usergroup()
	 */
	public function action_usergroup()
	{
		$view = \View::forge(PKGCOREPATH.'modules/bulk/views/bulk.php');

		\User\Model_Usergroup::disable_filter();
		//	\Locomo\Bulk::set_define_function('ctm_func');
		
		$form = $this->bulk(array(), array(), '\User\Model_Usergroup');

		$view->set_global('title', 'ユーザグループ設定');
		$view->set_global('form', $form, false);

		$view->set_safe('pagination', \Pagination::create_links());
		$view->set('hit', \Pagination::get('total_items')); ///
		$view->base_assign();
		$this->template->content = $view;
	}
}
