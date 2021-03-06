<?php
class Controller_Msgbrd extends \Locomo\Controller_Base
{
	// traits
	use \Controller_Traits_Testdata;
//	use \Controller_Traits_Wrkflw;
//	use \Controller_Traits_Revision;

	// locomo
	public static $locomo = array(
		'nicename' => 'メッセージボード', // for human's name
		'explanation' => 'メッセージボードのコントローラです', // use at admin/admin/home
		'main_action' => 'index_admin', // main action
		'main_action_name' => '管理一覧', // main action's name
		'main_action_explanation' => 'メッセージボードのトップです。', // explanation of top page
		'show_at_menu' => true,  // true: show at admin bar and admin/home
		'order' => 950,   // order of appearance
		'is_for_admin' => false, // true: place it admin's menu instead of normal menu
		'no_acl' => false, // true: admin's action. it will not appear at acl.
		'widgets' => array(
			array('name' => 'メッセージボードより', 'uri' => '\\Controller_Msgbrd/index_dashboard'),
		),
	);

	/**
	 * before()
	 */
	public function before()
	{
		parent::before();

		// check item's creator_id
		$pkid = \Request::main()->id;
		$obj = \Model_Msgbrd::find($pkid);
		if ( ! $obj) return false;

		// actions
		$actions = array(
			'\Controller_Msgbrd/delete',
			'\Controller_Msgbrd/edit',
		);

		// modify \Auth::get('allowed')
		\Auth::instance()->remove_allowed($actions);
		if ($obj->creator_id === \Auth::get('id'))
		{
			\Auth::instance()->add_allowed($actions);
		}
	}

	/**
	 * action_index_admin()
	 */
	public function action_index_admin()
	{
		parent::index_admin();
	}

	/**
	 * action_index_draft()
	 */
	public function action_index_draft()
	{
		// vals
		$model = $this->model_name;

		// set options
		$model::set_draft_options();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$shortname.'/index_admin');

		// title
		$title = static::$nicename.'の不可視項目';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_global('title', $title);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$this->template->set_safe('content', $content);
	}

	/**
	 * action_index_yet()
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

		$item = $this->template->content->item;

		if (!$item->is_opened())
		{
			Model_Msgbrd_Opened::forge(array(
				'user_id' => \Auth::get('id'),
				'msgbrd_id' => $item->id,
			))->save();
		}
	}

	/**
	 * action_create()
	 */
	public function action_create()
	{
		static::create();

		if ($parent = Model_Msgbrd::find(\Input::get('parent_id')))
		{
			$this->template->content->item->parent = $parent;
		}
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
	 * action_edit_categories()
	 */
	public function action_edit_categories($page = 1)
	{
		// bulk
		\Model_Msgbrd_Categories::$_options = array(
			'where' => array(array('is_available', 'is not', null)),
			'order_by' => array('seq' => 'ASC', 'name' => 'ASC'),
		);
		$this->model_name = '\Model_Msgbrd_Categories';
		$this->_content_template = 'msgbrd/categories/bulk';

		$this->bulk(array(
			'page' => $page,
		));

		// assign
		$presenter = \Presenter::forge($this->_content_template);
		$this->template->set_global('search_form', $presenter::search_form('カテゴリ設定'), false);
		$this->template->set_global('title', 'カテゴリ設定');
	}

	/**
	 * action_index_dashboard()
	 */
	public function action_index_dashboard()
	{
		\Model_Msgbrd::set_public_options();
		\Model_Msgbrd::$_options['order_by'] = array('created_at' => 'desc');

		$this->_content_template = 'msgbrd/index_admin_widget';
		parent::index_widget();
	}

	/*
	 * unopened() 未開封に
	 */
	public function action_unopened($id = null)
	{
		\DB::delete('lcm_msgbrds_opened')
			->where('user_id', \Auth::get('id'))
			->where('msgbrd_id', $id)
			->execute();

		\Session::set_flash('success', '未読にしました');
		return \Response::redirect(\Input::referrer());
	}
}
