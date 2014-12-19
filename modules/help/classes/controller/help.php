<?php
namespace Help;
class Controller_Help extends \Locomo\Controller_Base
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
	use \Revision\Traits_Controller_Revision;

	/*
	 * before()
	 */
	public function before()
	{
		parent::before();
		// revision use this
		$this->model_name = '\\Help\\Model_Help';
	}

	/*
	 * 新規作成
	 */
	public function action_create($id = NULL)
	{
		self::action_edit();
	}

	/*
	 * 編集
	 */
	public function action_edit($id = NULL)
	{
		parent::edit_core($id);
	}

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
		$help_path = '';
		$nicename = '';
		$action = '';
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
			if (
				property_exists($controller, 'locomo') &&
				$help_path = \Arr::get($controller::$locomo, 'help', false)
			)
			{
				$help_path = realpath(APPPATH.'../'.$help_path);
				$nicename = \Arr::get($controller::$locomo, 'nicename', '');
			}
		}

		// help text from default
		$help_texts = array();
		if ($help_path)
		{
			$help_texts[] = \Arr::get(\Fuel::load($help_path), strtolower($action), '');
		}

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

		// controller help index
		if (empty($body))
		{
			$module = \Inflector::get_modulename($locomo_path);
			$options = array();

/*
モジュールでなくコントローラを相手にしよう。
この上の単位でコントローラ一覧を並べよう。それがトップ。
でもって、aclもみるか？
aclをみる場合はそもそもviewを表示するときにもaclすべきか？
*/
			if ($module)
			{
				\Module::loaded($module) or \Module::load($module);
				foreach (\Module::get_controllers($module) as $kk => $vv)
				{
					if ( ! property_exists($kk, 'locomo')) continue;
					$nicename = $kk::$locomo['nicename'];
					$methods = \Arr::filter_prefixed(array_flip(get_class_methods($kk)), 'action_');
					foreach ($methods as $kkk => $vvv)
					{
						$key = urlencode(\Inflector::ctrl_to_safestr($kk.DS.$kkk));
						$options[$key] = $kkk;
					}
				}
			}
			else
			{

			}
		}



		// total help index
		if (empty($body))
		{

		}

		// related help

		// assign
		$content = \View::forge('index_admin');
		$content->base_assign();
		$content->set_global('title', $nicename.' &gt; '.$action);
		$content->set_safe('content', $body);
//		$this->template->content = $content;

echo $content;
exit;
	}
}
