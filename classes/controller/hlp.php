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
		$action = \Input::param('action');
		$obj = \Model_Hlp::find('first', array('where'=>array(array('ctrl', $action))));
		$id = @$obj->id ?: $id;

		$obj = parent::edit($id, $is_redirect = false);
		if ($obj && \Input::post())
		{
			static::$redirect = '/hlp/edit/'.$obj->id;
		}
///		$this->template->content->set('action', $action);
	}

	/**
	 * action_view()
	 */
	public function action_view()
	{
		// set default help from actionset
		$action = '';
		$help = '';
		$locomo_path = \Input::get('action');
		$controller = $locomo_path;
		if (strpos($locomo_path, '/'))
		{
			list($controller, $action) = explode('/', $locomo_path);
			$action = strtolower(str_replace('action_', '', $action));
		}
		$controller_original = \Inflector::safestr_to_ctrl($controller);
		$controller = str_replace('-Controller_', '\Help_', $controller);
		$controller = \Inflector::safestr_to_ctrl($controller);

		// title
		$target = str_replace('\Help_', '\Controller_', $controller);
		$title = Util::get_locomo($target, 'nicename');

		// module?
		$module = \Inflector::get_modulename($controller);
		if ($module) \Module::loaded($module) or \Module::load($module);

		// controller is not exist - help index
		if( ! $controller)
		{
			$content = \View::forge('hlp/index_admin');
			$content->set_global('title', 'ヘルプインデクス');
			$this->template->set_safe('content', $content);
			return;
		}
		elseif (class_exists($controller) && $action)
		{
		// fetch help text
			$hlp = new $controller;
			$help = property_exists($hlp, $action) ? $hlp->$action : '' ;
		}
		elseif (class_exists($controller))
		{
		// fetch each help index
			$hlp = new $controller;
			foreach ($hlp as $action => $v)
			{
				$key = \Inflector::ctrl_to_safestr($target.DS.$action);
				$items[$key] = $action;
			}
			$content = \View::forge('hlp/index_each');
			$content->set_global('title', $title.'ヘルプインデクス');
			$content->set('items', $items);
			$this->template->set_safe('content', $content);
			return;
		}

		$help = $help ?: 'この項目専用のヘルプは存在しません。画面内の指示に従って操作してください。' ;

		// additional help
		$controller_safe = substr($locomo_path, 0, strpos($locomo_path, '/'));
		$obj = \Model_Hlp::find('first', array('where'=>array(array('ctrl', $controller_safe))));

		// link to add additional help
		$add = '';
		if ($obj)
		{
//			$add.= html_tag('h2', array(), '加筆されたヘルプ') ;
			if (/*\Input::is_ajax() &&*/ \Auth::has_access('\\Controller_Hlp/edit'))
			{
				$add.= \Html::anchor(\Uri::create('/hlp/edit?action='.urlencode($controller_safe)), 'ヘルプを加筆・編集する',array('class'=>'edit_link'));
			}
			$add.= $obj->body ? html_tag('div', array('class' => 'add_body'), $obj->body) : '' ;
		}
		else
		{
			if (\Input::is_ajax() && \Auth::has_access('\\Help\\Controller_Hlp/edit'))
			{
				$add.= \Html::anchor(\Uri::create('/hlp/edit?action='.urlencode($controller_safe)), 'ヘルプを加筆・編集する',array('class'=>'edit_link'));
			}
		}
		$help = $add."\n".$help ;
		$help.= $action ? \Html::anchor(\Uri::create('/hlp/view?action='.$controller_safe), \Util::get_locomo($controller_original, 'nicename').'のヘルプ一覧', array('class'=>'helpindex_link')) : '' ;

		$help = html_tag('div', array('class' => 'txt'), \Markdown::parse($help));

		// is_ajax
		if (\Input::is_ajax())
		{
			echo $help;
			exit;
		}

		// title
		$title.= $action ? ' &gt; '.$action : '' ;

		// assign
		$content = \View::forge('hlp/view');
		$content->set_global('title', $title);
		$content->set_safe('content', $help);
		$this->template->set_safe('content', $content);
	}
}
