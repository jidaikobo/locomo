<?php
namespace Help;
class Controller_Help extends \Locomo\Controller_Crud
{
	// locomo
	public static $locomo = array(
		'show_at_menu' => false,
		'order_at_menu' => 1000,
		'is_for_admin' => false,
		'admin_home' => '\\Help\\Controller_Help/index_admin',
		'nicename' => 'ヘルプ',
		'actionset_classes' =>array(
			'base'   => '\\Help\\Actionset_Base_Help',
			'index'  => '\\Help\\Actionset_Index_Help',
			'option' => '\\Help\\Actionset_Option_Help',
		),
	);

	// trait
	use \Locomo\Controller_Traits_Testdata;
//	use \Option\Traits_Controller_Option;
//	use \Workflow\Traits_Controller_Workflow;
	use \Revision\Traits_Controller_Revision;

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		// parent
		parent::action_index_admin();

		// get default help
		$mod_or_ctrl = \Input::get('searches.mod_or_ctrl');
		$alls = \Util::get_mod_or_ctrl();
		$locomo = \Arr::get($alls, $mod_or_ctrl, array());
		$help_path = realpath(APPPATH.'../'.\Arr::get($locomo, 'help', false));
		if ( ! $help_path) return;

		$this->template->content->set_safe('default_help', file_get_contents($help_path));
	}
}
