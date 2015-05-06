<?php
namespace Locomo;
class Controller_Base extends Controller_Core
{
	public $_content_template = null;

	/**
	 * index()
	 */
	protected function index($page = 1)
	{
		// vals
		$model = $this->model_name;

		// set options
		$model::set_public_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'admin');

		// title
		$title = static::$nicename.'一覧';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$content->get_view()->set_global('title', $title);
		$this->template->content = $content;
	}

	/**
	 * index_admin()
	 */
	protected function index_admin($page = 1)
	{
		// vals
		$model = $this->model_name;

		// set options
		$model::set_public_options();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'index_admin');

		// title
		$title = static::$nicename.'管理一覧';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_global('title', $title);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$this->template->content = $content;
	}

	/**
	 * index_yet()
	 */
	protected function index_yet($page = 1)
	{
		// vals
		$model = $this->model_name;

		// exception
		if (!isset($model::properties()['created_at']) or !isset($model::properties()['expired_at'])) throw new \HttpNotFoundException;

		// set options
		$model::set_yet_options();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'index_admin');

		// title
		$title = static::$nicename.'予約項目';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_global('title', $title);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$this->template->content = $content;
	}

	/**
	 * index_expired()
	 */
	protected function index_expired($page = 1)
	{
		// vals
		$model = $this->model_name;

		// exception
		if (!isset($model::properties()['created_at']) or !isset($model::properties()['expired_at'])) throw new \HttpNotFoundException;

		// set options
		$model::set_expired_options();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'index_admin');

		// title
		$title = static::$nicename.'の期限切れ項目';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_global('title', $title);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$this->template->content = $content;
	}

	/**
	 * index_invisible()
	 */
	protected function index_invisible($page = 1)
	{
		// vals
		$model = $this->model_name;

		// exception
		if (!isset($model::properties()['is_visible'])) throw new \HttpNotFoundException;

		// set options
		$model::set_invisible_options();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'index_admin');

		// title
		$title = static::$nicename.'の不可視項目';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_global('title', $title);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$this->template->content = $content;
	}

	/**
	 * index_deleted()
	 */
	protected function index_deleted($page = 1)
	{
		// vals
		$model = $this->model_name;

		// exception
		if ( ! is_subclass_of($model, '\Orm\Model_Soft'))
		{
			throw new \Exception('対象モデルが\Orm\Model_Softを継承していないので、デフォルトのindex_delete()は使えません。\Orm\Model_Softを継承するか、コントローラごとに実装してください。');
		}

		// disable_filter() before find()
		$model::disable_filter();
		$deleted_column = $model::soft_delete_property('deleted_field', 'deletd_at');
		$model::$_options['where'][] = array($deleted_column, 'IS NOT', null);

		// set options
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'index_admin');

		// title
		$title = static::$nicename.'の削除済み項目';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_global('title', $title);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$this->template->content = $content;
	}

	/**
	 * index_all()
	 */
	protected function index_all($page = 1)
	{
		// vals
		$model = $this->model_name;

		// disable_filter() before find()
		if (is_subclass_of($model, '\Orm\Model_Soft'))
		{
			$model::disable_filter();
		}

		// set options
		$model::$_options = array();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'index_admin');

		// title
		$title = static::$nicename.'の削除済み項目';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_global('title', $title);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$this->template->content = $content;
	}

	/**
	 * index_widget()
	 * widget sample
	 */
	protected function index_widget()
	{
		// vals
		$model = $this->model_name;

		// widgets gives args by \Request::forge()->execute($args)
		if ($args = func_get_args())
		{
			if (is_array($args[0]))
			{
				$options = parse_str($args[0][1]);
			}
		}
		$options = $options ?: $model::$_options;

		// view
		$content = \View::forge(static::$dir.'index_admin_widget');
		$content->set('items', $model::find('all', $options));
		$this->template->content = $content;
		$this->template->set_global('title', static::$nicename.'管理一覧');

		// size
		$this->template->content->set('widget_size', $args[0][0]);
	}

	/**
	 * view()
	 */
	protected function view($id = null)
	{
		// redirect
		is_null($id) and \Response::redirect(static::$main_url);

		// vals
		$model = $this->model_name;

		// find()
		if ( ! $item = $model::find($id, $model::$_options))
		{
				// event
			$event = 'locomo_view_not_found';
			if (\Event::instance()->has_events($event)) \Event::instance()->trigger($event);

			// 403
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

		// set_object() - to generate menu at parent::base_assign()
		static::set_object($item);

		// plain
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'view');
		$plain = $content::plain($item);

		// view
		$content->get_view()->set_safe('plain', $plain);
		$content->get_view()->set('item', $item);
		$content->get_view()->set_global('title', self::$nicename.'閲覧');
		$this->template->content = $content;
	}

	/*
	 * create()
	 */
	protected function create()
	{
		$this->edit();
	}

	/*
	 * edit()
	 * @return mix succeed:object not found:null false:false
	 */
	protected function edit($id = null, $is_redirect = true)
	{
		// vals
		$model = $this->model_name ;
		$errors = array();

		// create or update
		if ($id)
		{
			$item = $model::find($id, $model::$_options);

			// not found
			if ( ! $item)
			{
				// event
				$event = 'locomo_edit_not_found';
				if (\Event::instance()->has_events($event)) \Event::instance()->trigger($event);

				// 403
				if ($is_redirect)
				{
					$page = \Request::forge('sys/403')->execute();
					$this->template->set_safe('content', $page);
					return new \Response($page, 403);
				}
				else
				{
					return null;
				}
			}
			$title = '#' . $id . ' ' . self::$nicename . '編集';
		}
		else
		{
			$item = $model::forge();
			$title = self::$nicename . '新規作成';
		}

		// prepare form and population
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'edit');
		$form = $content::form($item);

		// try to save
		if (\Input::post())
		{
			$item->cascade_set(\Input::post(), $form, $repopulate = true);

			// set errors
			$messages = array('ワンタイムトークンが失効しています。送信し直してみてください。');
			$errors = $form->error() ?: array() ;
			$errors = ! \Security::check_token() ? $errors + $messages : $errors ;

			// save
			if ( ! $errors && $item->save(null, true))
			{
				// event
				$event = 'locomo_edit_succeed';
				$item = \Event::instance()->has_events($event) ? \Event::instance()->trigger($event, $item) : $item ;
				\Session::set_flash('success', $id ? '更新しました。' : '新規作成しました。');
			}
			else
			{
				// event
				$event = 'locomo_edit_failed';
				if (\Event::instance()->has_events($event)) \Event::instance()->trigger($event);
				$errors[] = '更新を失敗しました。';
			}

			// set_flash()
			if ($errors) \Session::set_flash('error', $errors);

			// redirection or return
			if ( ! $errors && $item->id)
			{
				if ($is_redirect)
				{
					return \Response::redirect(static::$base_url.'edit/'.$item->id);
				}
			}
		}

		// set_object() - to generate menu at parent::base_assign()
		static::set_object($item);

		// view
		$content->get_view()->set_global('item', $item, false);
		$content->get_view()->set_global('form', $form, false);
		$content->get_view()->set_global('title', $title);
		$this->template->content = $content;

		// return
		return $errors ? false : $item;
	}

	/**
	 * delete()
	 * get action with JavaScript confirm
	 */
	protected function delete($id = null)
	{
		// redirect
		is_null($id) and \Response::redirect(static::$main_url);

		// vals
		$model = $this->model_name ;

		// find()
		if ( ! $item = $model::find($id, $model::$_options))
		{
			// 403
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

		// event
		$event = 'locomo_delete';
		if (\Event::instance()->has_events($event)) \Event::instance()->trigger($event, $item, 'none');

		// try to delete
		try {
			$item->delete(null, true);
		}
		catch (\Exception $e)
		{
			if (\Auth::is_root()) throw $e;
			\Session::set_flash('error', '項目の削除中にエラーが発生しました。');
			return \Response::redirect(static::$main_url);
		}

		// set_flash()
		\Session::set_flash('success', "#{$id}を削除しました");
		\Session::set_flash('affected_id', $id);

		// redirect
		if (method_exists(get_called_class(), 'action_index_deleted'))
		{
			return \Response::redirect(static::$base_url.'index_deleted');
		}
		else
		{
			return \Response::redirect(static::$main_url);
		}
	}

	/**
	 * undelete()
	 * get action with JavaScript confirm
	 */
	protected function undelete($id = null)
	{
		// redirect
		is_null($id) and \Response::redirect(static::$main_url);

		// vals
		$model = $this->model_name ;

		// check model instance
		if ( ! is_subclass_of($model, '\Orm\Model_Soft'))
		{
			throw new \Exception('対象モデルが\Orm\Model_Softを継承していないので、デフォルトのundelete()は使えません。\Orm\Model_Softを継承するか、コントローラごとに実装してください。');
		}

		// find()
		if ( ! $item = $model::find($id, $model::$_options))
		{
			// 403
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

		// event
		$event = 'locomo_undelete';
		if (\Event::instance()->has_events($event)) \Event::instance()->trigger($event, $item, 'none');

		// try to undelete
		try {
			$item->undelete(null, true);
		}
		catch (\Exception $e)
		{
			if (\Auth::is_root()) throw $e;
			\Session::set_flash('error', '項目の復活中にエラーが発生しました。');
			return \Response::redirect(static::$main_url);
		}

		// set_flash()
		\Session::set_flash('success', "#{$id}を復活しました");
		\Session::set_flash('affected_id', $id);

		// redirect
		return \Response::redirect(static::$main_url);
	}

	/**
	 * purge_confirm()
	 */
	protected function purge_confirm($id = null)
	{
		// redirect
		is_null($id) and \Response::redirect(static::$main_url);

		// vals
		$model = $this->model_name ;

		// check model instance
		if ( ! is_subclass_of($model, '\Orm\Model_Soft'))
		{
			// Model_Softを使っていない場合
			$is_purgable = $item = $model::find($id) ?: false;
		}
		else
		{
			// Model_Softを使っている場合はパージできるかチェック
			$is_purgable = true;
			$is_purgable = $item = $model::find_deleted($id) ?: false;
			if ( ! $is_purgable)
			{
				$is_purgable = $item = $model::find($id) ?: false;
			}
		}

		// redirect
		if ( ! $is_purgable)
		{
			\Session::set_flash('error', '完全削除中にエラーが発生しました。');
			return \Response::redirect(static::$main_url);
		}

		// set_object() - to generate menu at parent::base_assign()
		static::set_object($item);

		// form
		$form = \Fieldset::forge('confirm_submit');

		// set_flash
		\Session::set_flash('message', '完全に削除した項目は復活できません。この項目を完全に削除してもいいですか？');

		// view
		$content = \Presenter::forge($this->_content_template ?: 'defaults/purge');
		$content->set_safe('form', $form->build(static::$base_url.'purge/'.$item->id));
		$content->set_safe('plain', $content::plain($item));
		$content->set_safe('item', $item);
		$this->template->set_global('action', static::$base_url.'purge');
		$this->template->set_global('title', self::$nicename.'完全削除');
		$this->template->content = $content;
	}

	/**
	 * purge()
	 */
	protected function purge()
	{
		// redirect
		$id = \Input::post('id');
		if ( ! \Auth::is_root() || is_null($id) || ! \Security::check_token())
		{
			\Response::redirect(static::$main_url);
		}

		// vals
		$model = $this->model_name ;

		// check model instance
		if ( ! is_subclass_of($model, '\Orm\Model_Soft'))
		{
			// Model_Softを継承していない時には、そのまま削除を試みる
			$this->delete($id);
		}
		else
		{
			// try to purge
			if ($item = $model::find_deleted($id))
			{
				try {
					// 現状 Cascading deleteの恩恵を受けられない？ 要実装
					$item->purge(null, true);
				}
				catch (\Exception $e)
				{
					if (\Auth::is_root()) throw $e;
					\Session::set_flash('error', '完全削除中にエラーが発生しました');
					return \Response::redirect(static::$base_url.'index_deleted');
				}
	
				\Session::set_flash('success', '項目を完全に削除しました');
			}
			else
			{
				\Session::set_flash('error', '項目の完全削除中にエラーが発生しました');
			}
		}

		// redirect
		if (method_exists(get_called_class(), 'action_index_deleted'))
		{
			return \Response::redirect(static::$base_url.'index_deleted');
		}
		else
		{
			return \Response::redirect(static::$main_url);
		}
	}
}
