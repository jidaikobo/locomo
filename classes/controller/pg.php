<?php
namespace Locomo;
class Controller_Pg extends \Locomo\Controller_Base
{
	// traits
//	use \Controller_Traits_Testdata;
//	use \Controller_Traits_Wrkflw;
//	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'nicename' => 'ページ', // for human's name
		'explanation' => 'ページのコントローラです', // use at admin/admin/home
		'main_action' => 'index_admin', // main action
		'main_action_name' => '管理一覧', // main action's name
		'main_action_explanation' => 'ページのトップです。', // explanation of top page
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order' => 850,   // order of appearance
		'is_for_admin' => false, // true: place it admin's menu instead of normal menu
		'no_acl' => false, // true: admin's action. it will not appear at acl.
		'widgets' => array(
		),
	);

	/**
	 * pg_router()
	 * routerでの処理が改善したので、たぶんもう不要。はたらき支援ネットでしか使ってないので、小長井くんと削除する。
	 */
	public function pg_router()
	{
		// search content
		$path = \Input::get('path') ?: \Input::server('PATH_INFO');
		$path = $path ?: \Uri::segment(2);
		$path = str_replace('/pg/', '', rawurldecode($path));
		$model = $this->model_name;
		$model::set_authorized_options();
		$model::$_options['where'][] = array('path', $path);
		$item = $model::find('first', $model::$_options);

		// search deleted
		// Controller_Base::view()でもうちょっと厳密なチェックがあるので、ここではさらっと
		$column = \Arr::get($model::get_field_by_role('deleted_at'), 'lcm_field', 'deleted_at');
		if (
			! $item &&
			is_subclass_of($model, '\Orm\Model_Soft') &&
			isset($model::properties()[$column])
		)
		{
			$item = $model::find_deleted('first', $model::$_options);
		}
		return $item ? $item->id : -1 ;
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		static::index_admin();
	}

	/**
	 * action_index_yet()
	 */
	public function action_index_yet()
	{
		static::index_yet();
	}

	/**
	 * action_index_expired()
	 */
	public function action_index_expired()
	{
		static::index_expired();
	}

	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible()
	{
		static::$nicename = '一般非表示項目';
		static::index_invisible();
	}

	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted()
	{
		static::index_deleted();
	}

	/**
	 * action_index_unavailable()
	 */
	public function action_index_unavailable()
	{
		static::$nicename = '下書き項目';
		static::index_unavailable();
	}

	/*
	 * action_index_all()
	 */
	public function action_index_all()
	{
		static::index_all();
	}

	/**
	 * action_index_widget()
	 */
	public function action_index_widget()
	{
		static::index_widget(func_get_args());
	}

	/**
	 * action_view()
	 */
	public function action_view($id = null)
	{
		// TODO: 下位コンパチで残すけど一気に削除予定
		if ( ! is_numeric($id))
		{
			$id = $this->pg_router();
		}
		static::view($id);
	}

	/**
	 * action_create()
	 */
	public function action_create()
	{
		static::create();
	}

	/**
	 * action_edit()
	 */
	public function action_edit($id = null)
	{
		static::edit($id);
	}

	/**
	 * action_delete()
	 */
	public function action_delete($id = null)
	{
		static::delete($id);
	}

	/**
	 * action_undelete()
	 */
	public function action_undelete($id = null)
	{
		static::undelete($id);
	}

	/**
	 * action_purge_confirm()
	 */
	public function action_purge_confirm($id = null)
	{
		static::purge_confirm($id);
	}

	/**
	 * action_purge()
	 */
	public function action_purge($id = null)
	{
		static::purge($id);
	}
}
