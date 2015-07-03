<?php
class Controller_Adrs extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Testdata;
	use \Controller_Traits_Revision;
//	use \Controller_Traits_Wrkflw;

	// locomo
	public static $locomo = array(
		'nicename' => 'アドレス帳', // for human's name
		'explanation' => 'アドレス帳のコントローラです', // use at admin/admin/home
		'main_action' => 'index_admin', // main action
		'main_action_name' => '管理一覧', // main action's name
		'main_action_explanation' => 'アドレス帳のトップです。', // explanation of top page
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order' => 1000,   // order of appearance
		'is_for_admin' => false, // true: place it admin's menu instead of normal menu
		'no_acl' => false, // true: admin's action. it will not appear at acl.
		'widgets' => array(
		),
	);

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

	/**
	 * action_edit_adrsgrp()
	 */
	public function action_edit_adrsgrp($page = 1)
	{
		// bulk
		$this->model_name = '\Model_Adrsgrp';
		$this->_content_template = 'adrsgrp/bulk';
		$this->bulk($page, 0);

		// assign
		$presenter = \Presenter::forge($this->_content_template);
		$this->template->set_global('search_form', $presenter::search_form('アドレスグループ設定'), false);
		$this->template->set_global('title', 'アドレスグループ設定');
	}
}
