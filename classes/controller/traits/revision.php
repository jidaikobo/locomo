<?php
namespace Locomo;
trait Controller_Traits_Revision
{
	/**
	 * action_index_revision()
	 */
	public function action_index_revision($page = 1)
	{
		\Pagination::set('uri_segment', 'paged');

		// find_all_revisions
		$model = $this->model_name;

		\Model_Revision::set_paginated_options();
		\Model_Revision::$_options['order_by'] = array('created_at' => 'DESC');
		$items = \Model_Revision::find('all', \Model_Revision::$_options);

		foreach ($items as $item)
		{
			$rel_pk_col = $model::primary_key()[0];
			$rel_pk = $item->pk_id;

			$item->model_obj = $model::find('first', array('where' => array(
				array($rel_pk_col, $rel_pk),
			)));
		}

		if ( ! $items)
		{
			\Session::set_flash('error', '表示できませんでした');
			$redirect = \Arr::get(static::$locomo, 'main_action');
			$redirect = $redirect ? \Uri::create(\Inflector::ctrl_to_dir($redirect)) : static::$base_url;
			return \Response::redirect($redirect);
		}

		// search_form
		\Pagination::$refined_items = count($items);
		$search_form = \Model_Revision::search_form();

		// view
		$view = \View::forge('revision/index_revision');
		$view->set('items', $items);
		$view->set('base_url', static::$base_url);
		$view->set('subject', \Arr::get($model::get_field_by_role('subject'), 'lcm_field'));
		$this->template->content = $view;
		$this->template->content->set_safe('search_form', $search_form);
		$this->template->set_global('title', static::$locomo['nicename'].'履歴一覧');
	}

	/**
	 * action_each_index_revision()
	 */
	public function action_each_index_revision($id = null)
	{
		is_null($id) and \Response::redirect(static::$base_url);

		\Pagination::set('uri_segment', 'paged');
		\Model_Revision::$_options['where'][] = array(
			array('pk_id', '=', $id),
			array(array('model', \Inflector::add_head_backslash($this->model_name)), 'or' =>
				array('model', '\Locomo'.\Inflector::add_head_backslash($this->model_name))
			),
		);
		\Model_Revision::$_options['order_by'] = array('created_at' => 'DESC');
		// \Model_Revision::$_options['group_by'] = array('id');

		\Model_Revision::set_paginated_options();

		$items = \Model_Revision::find('all', \Model_Revision::$_options);
		if ( ! $items):
			\Session::set_flash('error', '履歴を取得できませんでした');
			$ret = method_exists(__CLASS__, 'action_view') ? static::$base_url.'view/'.$id : static::$main_url;
			return \Response::redirect($ret);
		endif;

		// unserialize data - to display usernames
		foreach($items as $k => $item):
			$items[$k]->data = unserialize($item->data);
			$modifier_name = \Model_Usr::get_display_name($item->user_id);
			$items[$k]->modifier_name = $modifier_name ?: 'deleted user #'.$item->user_id;
		endforeach;

		// add_actionset
		$action['urls'][] = \Html::anchor(static::$base_url.'index_revision/','履歴一覧へ');
		\Actionset::add_actionset(static::$controller, 'ctrl', $action);

		// subject field
		$model = $this->model_name;
		$subject = \Arr::get($model::get_field_by_role('subject'), 'lcm_field');
		if(empty($subject)) throw new \OutOfBoundsException($model.' doesn\'t have public static $_subject_field_name');

		// 現在名
		$single = $model::find($item->pk_id);
		$current_sbj = '';
		if ($single)
		{
			$current_sbj = $single->$subject;
		}

		// view
		$view = \View::forge('revision/each_index_revision');
		$view->set_global('field', \Arr::get($model::get_field_by_role('subject'), 'label'));
		$view->set_global('items', $items);
		$view->set_global('pk_id', $item->pk_id);
		$view->set_global('current_sbj', $current_sbj);
		$view->set_global('base_url', static::$base_url);
		$view->set_global('title', '履歴一覧');
		$view->set_global('subject', $subject);
		$this->template->content = $view;
	}

	/**
	 * action_view_revision()
	 */
	public function action_view_revision($revision_id = null)
	{
		is_null($revision_id) and \Response::redirect(static::$base_url);

		if ( ! $revisions = \Model_Revision::find($revision_id))
		{
			\Session::set_flash('error', '履歴を取得できませんでした');
			return \Response::redirect(static::$base_url);
		}

		// prepare data
		$model = $this->model_name;
		$data = unserialize($revisions->data);
		$obj = $model::forge();
		$obj->comment = '('.$revisions->operation.') '.$revisions->comment;

		$val = '';
		if ($data)
		{
			$obj->set($data);
		}

		// assign
		$content = \Presenter::forge($this->_content_template ?: 'revision/view_revision');
		$content->get_view()->set_safe('comment', $revisions->comment);
		$content->get_view()->set_safe('user', \Model_Usr::get_display_name($revisions->user_id));
		$content->get_view()->set_safe('plain', $content::plain($obj));
		$content->get_view()->set_global('item', $obj);
		$content->get_view()->set_global('title', '履歴個票');
		$content->get_view()->set_global('is_revision', true);

		// add_actionset
		$action['urls'][] = \Html::anchor(static::$base_url.'each_index_revision/'.$revisions->pk_id, '履歴一覧へ');
		\Actionset::add_actionset(static::$controller, 'ctrl', $action);

		// view
		$this->template->set_safe('content', $content);
	}
}
