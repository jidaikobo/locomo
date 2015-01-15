<?php
namespace Locomo;
class Controller_Hlp extends \Controller_Base
{
	// traits
	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'nicename'     => 'ヘルプ', // for human's name
		'main_action'  => 'view', // main action
		'show_at_menu' => false, // true: show at admin bar and admin/home
		'is_for_admin' => false, // true: hide from admin bar
		'order'        => 1000, // order of appearance
		'no_acl'       => true, // true: admin's action. it will not appear at acl.
	);

	/*
	 * before()
	 */
	public function before()
	{
		parent::before();
		// revision use this
		$this->model_name = '\\Model_Hlp';
	}

	/*
	 * action_edit()
	 */
	public function action_edit($id = NULL)
	{
		$action = urlencode(\Input::param('action'));
		$ctrl = \Inflector::words_to_upper(substr($action, 0, strpos($action, '%')));
		$obj = \Model_Hlp::find('first', array('where'=>array(array('ctrl', $ctrl))));
		$id = @$obj->id ?: '';
		$redirect = '/hlp/edit?action='.$action;
		parent::edit($id, $redirect);
		$this->template->content->set('action', $action);
	}

	/**
	 * action_view()
	 */
	public function action_view()
	{
/*
include(LOCOMOPATH.'migrations/005_create_help.php');
include(LOCOMOPATH.'migrations/006_create_hlp.php');
$h = new \Fuel\Migrations\Create_hlp();
$h->up();
*/
		// set default help from actionset
		$locomo_path_raw = \Input::get('action');
		$locomo_path = \Inflector::safestr_to_ctrl($locomo_path_raw);
		$controller = $locomo_path;
		$nicename = '';
		$action = '';
		$actionsets = array();
		if (strpos($locomo_path, '/') !== false)
		{
			// get actionset
			list($controller, $action) = explode('/', $locomo_path);
			$action = strtolower($action);
		}

		// controller is not exist
		if( ! $controller)
		{
			$content = \View::forge('hlp/view');
			$this->base_assign();
			$content->set_global('title', 'ヘルプインデクス');
			$content->set_safe('content', '');
			$this->template->content = $content;
			return;
		}
		else
		{
			$actionsets = \Actionset::get_actionset($controller);
		}

		// get action from actionset
		$help = '';
		if ($actionsets)
		{
			foreach ($actionsets as $realm => $v)
			{
				if ($action)
				{
					$help.= \Arr::get($v, strtolower($action).'.action_name');
					$help.= \Arr::get($v, strtolower($action).'.help');
				}
				else
				{
					foreach ($v as $kk => $vv)
					{
						$txt = \Arr::get($v, strtolower($kk).'.help');
						if ($txt)
						{
							$help.= html_tag('h3', array(), \Arr::get($v, strtolower($kk).'.action_name'));
							$help.= html_tag('div', array(), $txt);
						}
					}
				}
			}
		}

		$help = $help ?: 'この項目専用のヘルプは存在しません。' ;
		$help = html_tag('div', array('class' => 'txt'), \Markdown::parse($help));

		// additional help
		$controller_safe = \Inflector::ctrl_to_safestr($controller);
		$obj = \Model_Hlp::find('first', array('where'=>array(array('ctrl', $controller_safe))));

		// link to add additional help
		$add = '';
		$add.= $action ? \Html::anchor(\Uri::create('/hlp/view?action='.$controller_safe), 'コントローラヘルプ') : '' ;
		if ($obj)
		{
			$add.= html_tag('h2', array(), '加筆されたヘルプ') ;
			if (\Auth::instance()->has_access('\\Controller_Hlp/edit'))
			{
				$add.= \Html::anchor(\Uri::create('/hlp/edit?action='.urlencode($locomo_path_raw)), '編集する');
			}
			$add.= html_tag('div', array('class' => 'add_body'), $obj->body) ;
		}
		else
		{
			if (\Auth::instance()->has_access('\\Help\\Controller_Hlp/edit'))
			{
				$add.= ' | '.\Html::anchor(\Uri::create('/hlp/edit?action='.urlencode($locomo_path_raw)), 'ヘルプを加筆する');
			}
		}
		$help.= $add ;

		// is_ajax
		if (\Input::is_ajax())
		{
			echo $help;
			exit;
		}

		// title
		$title = $controller::$locomo['nicename'];
		$title.= $action ? ' &gt; '.$action : '' ;

		// assign
		$content = \View::forge('hlp/view');
		$this->base_assign();
		$content->set_global('title', $title);
		$content->set_safe('content', $help);
		$this->template->content = $content;
	}
}