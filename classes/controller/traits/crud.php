<?php
namespace Locomo;
trait Controller_Traits_Crud
{
	/**
	 * action_index_admin()
	 * 管理者用の index
	 * @param $options
	 * @param $model
	 * @param $deleted
	 */
	public function action_index_admin()
	{
		parent::index_admin();
	}

	/**
	 * action_index()
	 */
	public function action_index()
	{
		parent::index_admin();
	}

	/**
	 * action_index_yet()
	 * 予約項目
	 * created_at が未来のもの
	 */
	public function action_index_yet()
	{
		parent::index_yet();
	}

	/**
	 * action_index_expired()
	 */
	public function action_index_expired()
	{
		parent::index_expired();
	}

	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible()
	{
		parent::index_invisible();
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
		parent::view();
	}

	/**
	 * action_create()
	 */
	public function action_create($id = null)
	{
		parent::create($id);
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
	 * post only
	 * need csrf token
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
	public function action_purge_confirm ($id = null)
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
