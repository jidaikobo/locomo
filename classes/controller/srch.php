<?php
namespace Locomo;
class Controller_Srch extends \Locomo\Controller_Base
{
	// traits
//	use \Controller_Traits_Testdata;
//	use \Controller_Traits_Wrkflw;
//	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'nicename' => '検索センター', // for human's name
		'explanation' => '検索センターのコントローラです', // use at admin/admin/home
		'main_action' => 'index_admin', // main action
		'main_action_name' => '検索センター管理一覧', // main action's name
		'main_action_explanation' => '検索センターのトップです。', // explanation of top page
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order' => 1030,   // order of appearance
		'is_for_admin' => true, // true: place it admin's menu instead of normal menu
		'no_acl' => true, // true: admin's action. it will not appear at acl.
		'widgets' => array(
		),
	);

	/**
	 * action_sync()
	 */
	public function action_sync()
	{
		$cnt = 0;
		if (\Input::post())
		{
			// vals
			$models = array();

			// modules
			$paths = \Module::get_exists();
			if ($paths)
			{
				foreach ($paths as $path)
				{
					$model_path = $path.'/classes/model';
					if ( ! file_exists($model_path)) continue;
					foreach (\Util::get_file_list($model_path) as $v)
					{
						if (is_dir($v)) continue;
						$models[] = \Inflector::path_to_classname($v, 'model');
					}
				}
			}

			// ordinally models
			$paths = array_merge(
				\Inflector::dir_to_ctrl(APPPATH.'classes'.DS.'model'),
				\Inflector::dir_to_ctrl(LOCOMOPATH.'classes'.DS.'model')
			);
			foreach ($paths as $path)
			{
				$models[] = \Inflector::path_to_classname($path, 'model');
			}

			// sync
			foreach ($models as $model)
			{
				// seacrh model which has a Observer_Srch
				if ( ! class_exists($model))
				{
					$suspicious = explode('\\', $model);
					if (count($suspicious) < 2) continue;
					\Module::exists($suspicious[1]) and \Module::load($suspicious[1]);
				}
				if ( ! class_exists($model)) continue;
				if ( ! isset($model::observers()['Locomo\Observer_Srch'])) continue;

				// sync
				$items = $model::find('all');
				foreach ($items as $item)
				{
					if ($item->save()) $cnt++;
				}
			}
		}

		// view
		$content = \View::forge(static::$dir.'sync');

		// title
		$title = '検索センターの同期';

		// view
		$content->set_global('cnt', $cnt);
		$content->set_global('title', $title);
		$this->template->set_safe('content', $content);
	}

	/**
	 * action_index()
	 */
	public function action_index()
	{
		parent::index();
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		parent::index_admin();
	}

	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted()
	{
		parent::index_deleted();
	}

	/*
	 * action_index_all()
	 */
	public function action_index_all()
	{
		parent::index_all();
	}

	/**
	 * action_view()
	 */
	public function action_view($id = null)
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
	public function action_edit($id = null)
	{
		parent::edit($id);
	}

	/**
	 * action_delete()
	 */
	public function action_delete($id = null)
	{
		parent::delete($id);
	}

	/**
	 * action_undelete()
	 */
	public function action_undelete($id = null)
	{
		parent::undelete($id);
	}

	/**
	 * action_purge_confirm()
	 */
	public function action_purge_confirm($id = null)
	{
		parent::purge_confirm($id);
	}

	/**
	 * action_purge()
	 */
	public function action_purge($id = null)
	{
		parent::purge($id);
	}
}
