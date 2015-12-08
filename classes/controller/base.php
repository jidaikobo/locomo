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
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items && ! \Request::is_hmvc()) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'index');

		// title
		$title = static::$nicename.'一覧';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$content->get_view()->set_global('title', $title);
		$this->template->set_safe('content', $content);
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

		if ( ! $items && ! \Request::is_hmvc()) \Session::set_flash('message', '項目が存在しません。');

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
		$this->template->set_safe('content', $content);
	}

	/**
	 * index_yet()
	 */
	protected function index_yet($page = 1)
	{
		// vals
		$model = $this->model_name;

		// exception
		$column_created_at = \Arr::get($model::get_field_by_role('created_at'), 'lcm_field', 'created_at');
		$column_expired_at = \Arr::get($model::get_field_by_role('expired_at'), 'lcm_field', 'expired_at');
		if (
			! isset($model::properties()[$column_created_at]) or
			! isset($model::properties()[$column_expired_at])
		)
		{
			throw new \HttpNotFoundException;
		}

		// set options
		$model::set_yet_options();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items && ! \Request::is_hmvc()) \Session::set_flash('message', '項目が存在しません。');

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
		$this->template->set_safe('content', $content);
	}

	/**
	 * index_expired()
	 */
	protected function index_expired($page = 1)
	{
		// vals
		$model = $this->model_name;

		// exception
		$column_created_at = \Arr::get($model::get_field_by_role('created_at'), 'lcm_field', 'created_at');
		$column_expired_at = \Arr::get($model::get_field_by_role('expired_at'), 'lcm_field', 'expired_at');
		if (
			! isset($model::properties()[$column_created_at]) or
			! isset($model::properties()[$column_expired_at])
		)
		{
			throw new \HttpNotFoundException;
		}

		// set options
		$model::set_expired_options();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items && ! \Request::is_hmvc()) \Session::set_flash('message', '項目が存在しません。');

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
		$this->template->set_safe('content', $content);
	}

	/**
	 * index_invisible()
	 */
	protected function index_invisible($page = 1)
	{
		// vals
		$model = $this->model_name;

		// exception
		$column = \Arr::get($model::get_field_by_role('is_visible'), 'lcm_field', 'is_visible');
		if (!isset($model::properties()[$column])) throw new \HttpNotFoundException;

		// set options
		$model::set_invisible_options();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items && ! \Request::is_hmvc()) \Session::set_flash('message', '項目が存在しません。');

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
		$this->template->set_safe('content', $content);
	}

	/**
	 * index_unavailable()
	 */
	protected function index_unavailable($page = 1)
	{
		// vals
		$model = $this->model_name;

		// exception
		$column = \Arr::get($model::get_field_by_role('is_available'), 'lcm_field', 'is_available');
		if (!isset($model::properties()[$column])) throw new \HttpNotFoundException;

		// set options
		$model::set_unavailable_options();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items && ! \Request::is_hmvc()) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'index_admin');

		// title
		$title = static::$nicename.'の停止中項目';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_global('title', $title);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$this->template->set_safe('content', $content);
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

		// set options
		$model::set_deleted_options();
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items && ! \Request::is_hmvc()) \Session::set_flash('message', '項目が存在しません。');

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
		$this->template->set_safe('content', $content);
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
		$model::set_search_options();
		$model::set_paginated_options();

		// find()
		$items = $model::find('all', $model::$_options) ;
		if ( ! $items && ! \Request::is_hmvc()) \Session::set_flash('message', '項目が存在しません。');

		// refined count
		\Pagination::$refined_items = count($items);

		// presenter
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'index_admin');

		// title
		$title = static::$nicename.'のすべての項目';

		// view
		$content->get_view()->set('items', $items);
		$content->get_view()->set_global('title', $title);
		$content->get_view()->set_safe('search_form', $content::search_form($title));
		$this->template->set_safe('content', $content);
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
		$options = array();
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
		$this->template->set_safe('content', $content);
		$this->template->set_global('title', static::$nicename.'管理一覧');

		// size
		$size = isset($args[0][0]) ? $args[0][0] : 1 ;
		$this->template->content->set('widget_size', $size);
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
		$model::set_authorized_options();
		$model::$_options['from_cache'] = false;
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

		// set_object() - to generate menu at \Presenter_header::view() called at parent::after()
		static::set_object($item);

		// plain
		$content = \Presenter::forge($this->_content_template ?: static::$dir.'view');
		$plain = $content::plain($item);

		// view
		$content->get_view()->set_safe('plain', $plain);
		$content->get_view()->set('item', $item);
		$content->get_view()->set_global('title', '#'.$id.' '.self::$nicename.'閲覧');
		$this->template->set_safe('content', $content);
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
			$model::set_authorized_options();
			$model::$_options['from_cache'] = false;
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

		// set_object() - to generate menu at \Presenter_header::view() called at parent::after()
		static::set_object($item);

		// view
		$content->get_view()->set_global('item', $item, false);
		$content->get_view()->set_global('form', $form, false);
		$content->get_view()->set_global('title', $title);
		$this->template->set_safe('content', $content);

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
		$model::set_authorized_options();
		$model::$_options['from_cache'] = false;
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
		$model::set_authorized_options();
		$model::$_options['from_cache'] = false;
		if ( ! $item = $model::find_deleted($id, $model::$_options))
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

		// set_object() - to generate menu at \Presenter_header::view() called at parent::after()
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
		$this->template->set_safe('content', $content);
	}

	/**
	 * purge()
	 */
	protected function purge()
	{
		// redirect
		$id = \Input::post('id');
		if (
			! \Auth::instance()->has_access(static::$controller.DS.static::$action) ||
			is_null($id) ||
			! \Security::check_token()
		)
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

	/**
	 * bulk()
	 */
	protected function bulk($page = 1, $add = 3, $is_redirect = true)
	{
		$model = $this->model_name;
		$action = \Request::main()->action;
		$is_model_soft = is_subclass_of($model, '\Orm\Model_Soft');

		$model::set_authorized_options();

		// save から戻ってきた時の処理
		if (\Input::get('ids'))
		{
			$model::$_options['where'] = array(array($model::primary_key()[0], 'IN', \Input::get('ids')));
			// $pagination_config['per_page'] = count(\Input::get('ids')) * 2;

			if ($is_model_soft) $model::disable_filter();

			$model::set_paginated_options();
			$objects = $model::find('all', $model::$_options);

			if ($is_model_soft && ! $model::get_filter_status()) $model::enable_filter();
		// edit create 分岐
		}
		// create
		elseif ($create_field = intval(\Input::get('create', 0)))
		{
			for ($i = 0; $i < $create_field; $i++)
			{
				$objects[] = $model::forge();
			}
		}
		// edit
		else
		{
			$model::set_search_options();
			$model::set_paginated_options();
			$objects = $model::find('all', $model::$_options);
			// $total = max($total - count($objects), 1);
			if ($add)
			{
				for ($i = 0; $i < $add; $i++)
				{
					$objects[] = $model::forge();
				}
			}
		}

		if ( ! $objects)
		{
			\Session::set_flash('message', array(
				array(
					'該当が 0 件でした。新規作成してください。',
					static::$current_url.'?create=1')
				)
			);
		}

		// forge bulk
		$bulk = \Locomo\Bulk::forge();
		$bulk::$_presenter = $this->_content_template ?: static::$dir.'bulk';
		$bulk->add_model($objects);

		// count all for pagination
		if (method_exists($model, 'set_public_options'))
		{
			$model::set_public_options();
			$options = array();
			if (isset($model::$_options['where'])) $options['where'] = $model::$_options['where'];
			\Pagination::$refined_items = $model::count($options);
		}

		// deletedも保持
		$ids = array();
		foreach ($objects as $object)
		{
			!is_null($object->{$object::primary_key()[0]}) and $ids[] = $object->{$object::primary_key()[0]};
		}

		if (\Input::post() && \Security::check_token())
		{
			if ($bulk->save())
			{
				// saveした object の保持
				// $ids = array();
				foreach ($objects as $object)
				{
					!is_null($object->{$object::primary_key()[0]}) and $ids[] = $object->{$object::primary_key()[0]};
				}

				$ids = array_unique($ids);

				// 新規を全て空で保存した時の処理
				$judge = array_filter($ids);
				if (empty($judge))
				{
					\Session::set_flash('error', '保存対象が 0 件です');
					$url = \Uri::create(static::$base_url.$action, array(), \Input::get());
					if ($is_redirect) return \Response::redirect($url);
				}

				\Session::set_flash('success', self::$nicename . 'への変更を' . count($ids) . '件保存しました');

				$url = \Uri::create(static::$base_url.$action, array(), array('ids' => $ids));
				if ($is_redirect) return \Response::redirect($url);
			}
			else
			{
				\Session::set_flash('error', self::$nicename . 'の保存に失敗しました。エラーメッセージを参照して下さい。');
			}
		}

		$content = \View::forge($this->_content_template ?: static::$dir.'bulk');
		$this->template->content = $content;
		$this->template->set_global('form', $bulk->build(), false);
		$this->template->set_global('title', self::$nicename.'の一括処理');
	}

	/*
	 *
	 * 条件1 $options related の一つ目にマスタの relaten
	 */
	protected function conditioned_bulk($options, $defaults = array(), $belongs_to_display_name = 'name', $is_redirect = true)
	{

		$model = $this->model_name;
		$model::set_authorized_options();


		$related =  array_keys($options['related'])[0];
		$rel_model = $model::relations($related)->model_to;
		$rel_key_from = $model::relations($related)->key_from[0];
		$rel_key_to = $model::relations($related)->key_to[0];

		$rel_datas = $rel_model::find('all', $options['related'][$related]);


	//	($rel_model::primary_key()[0]);

		$objects = array();

		foreach ($rel_datas as $data) {

			$master_options = $options;
			$master_options['where'][] = array($rel_key_from, '=', $data->{$rel_key_to});
			$master_options['related'][$related]['from_cache'] = false;//[] = array($rel_key_to, '=', $data->{$rel_key_to});
			$model_options = \Arr::merge($model::$_options, $master_options);
			$model_options['from_cache'] = false;

			$row = $model::find('first', $model_options, false);
			if (!$row) {
				$row = $model::forge(array(
					$rel_key_from => $data->{$rel_key_to},
				));
			}
			foreach ($defaults as $key => $default) {
				$row->{$key} = $default;
			}

			$objects[] = $row;
		}

		$bulk = \Locomo\Bulk::forge();
		$bulk::$_presenter = $this->_content_template ?: static::$dir.'bulk';
		$bulk->add_model($objects, false);

		if (\Input::post() && \Security::check_token())
		{
			if ($bulk->save())
			{

				\Session::set_flash('success', self::$nicename . 'への変更を保存しました');

				$action = \Request::main()->action;

				$url = \Uri::create(static::$base_url.$action, array(), \Input::get());
				if ($is_redirect) return \Response::redirect($url);
			}
			else
			{
				\Session::set_flash('error', self::$nicename . 'の保存に失敗しました。エラーメッセージを参照して下さい。');
			}
		}

		$content = \View::forge(static::$dir.'bulk');
		$this->template->content = $content;
		$this->template->set_global('form', $bulk->build(), false);
		$this->template->set_global('title', self::$nicename.'の一括処理');
	}


}
