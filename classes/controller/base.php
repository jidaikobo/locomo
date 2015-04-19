<?php
namespace Locomo;
class Controller_Base extends Controller_Core
{
	public $_content_template = null;

	/**
	 * before()
	 */
	public function before()
	{
		parent::before();

		if (is_null($this->pagination_config)) {
			$suspicious_segment = \Arr::search(\Uri::segments(), \Request::main()->action) + 2;
			\Pagination::set_config('uri_segment', $suspicious_segment);
		}
	}

	/**
	 * index()
	 */
	protected function index($page)
	{
		// vals
		$model = $this->model_name;

		// genereate paginated option and links of \Pagination::create_links()
		\Pagination::set_options($model);

/*
		// free word search - sample
		$all = \Input::get('all') ? '%'.\Input::get('all').'%' : '' ;
		if ($all)
		{
			$model::$_options['where'][] = array(
				array('name', 'LIKE', $all),
				'or' => array(
					array('kana', 'LIKE', $all),
					'or' => array(
					) 
				) 
			);
		}
*/

		// view
		$content = \View::forge($this->_content_template ?: static::$shortname.'/index');
		$content->set('items', $model::find('all', $model::$_options));
		$content->set_safe('search_form', $model::search_form());
		$this->template->content = $content;
		$this->template->set_global('title', static::$nicename.'一覧');
	}

	/**
	 * index_admin()
	 */
	protected function index_admin($page)
	{
		$this->_content_template = static::$shortname.'/index_admin';
		static::index($page);
		$this->template->set_global('title', static::$nicename.'管理一覧');
	}

	/**
	 * index_yet()
	 * 予約項目
	 * created_at が未来のもの
	 */
	protected function index_yet()
	{
		// exception
		if (!isset($model::properties()['created_at']) or !isset($model::properties()['expired_at'])) throw new \HttpNotFoundException;

		// vals
		$model = $this->model_name;

		// genereate paginated option and links of \Pagination::create_links()
		\Pagination::set_options($model);
		$model::$_options['where'][] = array('created_at', '>=', date('Y-m-d H:i:s'));
		$model::$_options['where'][] = array('expired_at', 'is', null);

		// view
		$content = \View::forge($this->_content_template ?: static::$shortname.'/index_admin');
		$content->set('items', $model::find('all', $model::$_options));
		$content->set_safe('search_form', $model::search_form());
		$this->template->content = $content;
		$this->template->set_global('title', static::$nicename.'予約項目');
	}

	/**
	 * index_expired()
	 */
	protected function index_expired()
	{
		// exception
		if (!isset($model::properties()['created_at']) or !isset($model::properties()['expired_at'])) throw new \HttpNotFoundException;

		// vals
		$model = $this->model_name;

		// genereate paginated option and links of \Pagination::create_links()
		\Pagination::set_options($model);
		$model::$_options['where'][] = array('expired_at', '<', date('Y-m-d H:i:s'));

		// view
		$content = \View::forge($this->_content_template ?: static::$shortname.'/index_admin');
		$content->set('items', $model::find('all', $model::$_options));
		$content->set_safe('search_form', $model::search_form());
		$this->template->content = $content;
		$this->template->set_global('title', static::$nicename.'の期限切れ項目');
	}

	/**
	 * index_invisible()
	 */
	protected function index_invisible()
	{
		// exception
		if (!isset($model::properties()['is_visible'])) throw new \HttpNotFoundException;

		// vals
		$model = $this->model_name;

		// genereate paginated option and links of \Pagination::create_links()
		\Pagination::set_options($model);
		$model::$_options['where'][] = array('is_visible', '=', 0);

		// view
		$content = \View::forge($this->_content_template ?: static::$shortname.'/index_admin');
		$content->set('items', $model::find('all', $model::$_options));
		$content->set_safe('search_form', $model::search_form());
		$this->template->content = $content;
		$this->template->set_global('title', static::$nicename.'の不可視項目');
	}

	/**
	 * index_deleted()
	 */
	protected function index_deleted()
	{
		// exception
		if ($model instanceof \Orm\Model_Soft) throw new \HttpNotFoundException;

		// vals
		$model = $this->model_name;

		// genereate paginated option and links of \Pagination::create_links()
		$deleted_column = $model::soft_delete_property('deleted_field', 'deletd_at');
		\Pagination::set_options($model);
		$model::$_options['where'][] = array($deleted_column, 'IS NOT', null);

		// disable_filter() before find()
		$model::disable_filter();

		// view
		$content = \View::forge($this->_content_template ?: static::$shortname.'/index_admin');
		$content->set('items', $model::find('all', $model::$_options));
		$content->set_safe('search_form', $model::search_form());
		$this->template->content = $content;
		$this->template->set_global('title', static::$nicename.'の削除済み項目');
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
				$options = $args;
			}
			else
			{
				parse_str($args[0], $q);
				$options = $q;
			}
		}
		else
		{
			$options = $model::$_options;
		}

		// view
		$content = \View::forge(static::$shortname.'/index_admin_widget');
		$content->set('items', $model::find('all', $options));
		$this->template->content = $content;
		$this->template->set_global('title', static::$nicename.'管理一覧');
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

		// view
		$content = \View::forge(static::$shortname.'/view');
		$content->set_safe('plain', $model::plain_definition('view', $item)->build_plain());
		$content->set('item', $item);
		$content->set_global('title', self::$nicename.'閲覧');
		$this->template->content = $content;
	}

	/*
	 * edit()
	 */
	protected function edit($id = null)
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
				$page = \Request::forge('sys/403')->execute();
				$this->template->set_safe('content', $page);
				return new \Response($page, 403);
			}
			$title = '#' . $id . ' ' . self::$nicename . '編集';
		}
		else
		{
			$item = $model::forge();
			$title = self::$nicename . '新規作成';
		}

		// prepare form and population
		$form = $model::form_definition('edit', $item);

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
				\Session::set_flash('success', '更新しました。');
			}
			else
			{
				// event
				$event = 'locomo_edit_failed';
				if (\Event::instance()->has_events($event)) \Event::instance()->trigger($event);
				$errors[] = '更新を失敗しました。';
			}
		}

		// set_flash()
		if ($errors) \Session::set_flash('error', $errors);

		// set_object() - to generate menu at parent::base_assign()
		static::set_object($item);

		// view
		$content = \View::forge(static::$shortname.'/edit');
		$content->set_global('item', $item, false);
		$content->set_global('form', $form, false);
		$this->template->set_global('title', $title);
		$this->template->content = $content;
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
		if ( ! $model instanceof \Orm\Model_Soft)
		{
			\Session::set_flash('error', 'モデルが対応していないため削除された項目は復活できません');
			return \Response::redirect(static::$main_url);
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
	protected function purge_confirm ($id = null)
	{
		// redirect
		is_null($id) and \Response::redirect(static::$main_url);

		// vals
		$model = $this->model_name ;

		// check model instance
		if ( ! $model instanceof \Orm\Model_Soft)
		{
			\Session::set_flash('error', 'モデルが対応していないため完全な削除はできません。');
			return \Response::redirect(static::$main_url);
		}

		// purgable check
		$is_purgable = true;
		$is_purgable = $item = $model::find_deleted($id) ?: false;
		if ( ! $is_purgable)
		{
			$is_purgable = $item = $model::find($id) ?: false;
		}
		if ( ! $is_purgable)
		{
			\Session::set_flash('error', '完全削除中にエラーが発生しました。');
			return \Response::redirect(static::$main_url);
		}

		// set_object() - to generate menu at parent::base_assign()
		static::set_object($item);

		// form
		$form = \Fieldset::forge('confirm_submit');

		// view
		$content = \View::forge('defaults/purge');
		$content->set_safe('form', $form->build(static::$base_url.'purge/'.$item->id));
		$content->set_safe('plain', $model::plain_definition('purge_confirm', $item)->build_plain());
		$this->template->set_global('action', static::$base_url.'purge/'.$item->id);
		$this->template->set_global('title', self::$nicename.'完全削除');
		$this->template->content = $content;
	}

	/**
	 * purge()
	 */
	protected function purge($id = null)
	{
		// redirect
		is_null($id) and \Response::redirect(static::$main_url);

		// vals
		$model = $this->model_name ;

		// check model instance
		if ( ! $model instanceof \Orm\Model_Soft)
		{
			\Session::set_flash('error', 'モデルが対応していないため完全な削除はできません。');
			return \Response::redirect(static::$main_url);
		}

		// try to purge
		if (
			\Auth::is_root()
			and \Input::post()
			and \Security::check_token()
			and $obj = $model::find_deleted($id)
		)
		{
			try {
				// 現状 Cascading deleteの恩恵を受けられない？ 要実装
				$item->purge(null, true);
			}
			catch (\Exception $e)
			{
				\Session::set_flash('error', '完全削除中にエラーが発生しました');
				return \Response::redirect(static::$base_url.'index_deleted');
			}

			\Session::set_flash('success', '項目を完全に削除しました');
		}
		else
		{
			\Session::set_flash('error', '項目の完全削除中にエラーが発生しました');
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
