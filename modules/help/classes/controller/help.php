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
/*
include(LOCOMOPATH.'migrations/005_create_help.php');
$h = new \Fuel\Migrations\Create_help();
$h->up();
*/
		// set default help
		$locomo_path_raw = \Input::get('searches.action');
		$locomo_path = \Inflector::safestr_to_ctrl($locomo_path_raw);
		if (strpos($locomo_path, '/') !== false)
		{
			// each index
			list($controller, $action) = explode('/', $locomo_path);
			$action = strtolower($action);
			$locomo_path = $controller.'/'.$action;

			// module?
			if ($module = \Inflector::get_modulename($controller))
			{
				\Module::loaded($module) or \Module::load($module);
			}

			// $locomo
			if (property_exists($controller, 'locomo'))
			{
				$help_path = realpath(APPPATH.'../'.\Arr::get($controller::$locomo, 'help', false));
				$nicename = \Arr::get($controller::$locomo, 'nicename', '');
			}
		}

		// help text from default
		$help_texts = array();
		$help_texts[] = \Arr::get(\Fuel::load($help_path), strtolower($action), '');

		$body = '';
		if (\Arr::get($help_texts, '0.title'))
		{
			$body.= \Markdown::parse('#'.$help_texts[0]['title']);
			$body.= \Markdown::parse($help_texts[0]['body']);
		}

		// help text from database
		$objs = \Help\Model_Help::find('all', array('where' => array(array('action', urlencode($locomo_path_raw)),), 'order_by' => array('seq' => 'asc'),));

		foreach ($objs as $obj)
		{
			$link = html_tag('span', array('class' => "edit_help"), \Html::anchor(\Uri::create('/help/help/edit/'.$obj->id), '編集する'));
			$title = html_tag('h2', array(), $obj->title.$link);
			$body.= $title;
			$body.= $obj->body;
		}

		// total help index
		if (empty($body))
		{

		}

		$content = \View::forge('index_admin');
		$content->base_assign();
		$this->template->set_global('title', $nicename.' &gt; '.$action);
		$this->template->set_safe('content', $body);



/*
		// parent
		parent::action_index_admin();

		// get default help
		$mod_or_ctrl = \Input::get('searches.mod_or_ctrl');
		$alls = \Util::get_mod_or_ctrl();
		$locomo = \Arr::get($alls, $mod_or_ctrl, array());
		$help_path = realpath(APPPATH.'../'.\Arr::get($locomo, 'help', false));
		if ( ! $help_path) return;

		$this->template->content->set_safe('default_help', file_get_contents($help_path));
*/
	}
}
