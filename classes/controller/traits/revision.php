<?php
namespace Locomo;
trait Controller_Traits_Revision
{
	/**
	 * action_index_revision()
	 */
	public function action_index_revision($page = 1)
	{
		// find_all_revisions
		$model = $this->model_name;
		$view = \View::forge('revision/index_revision');
		$view = \Model_Revision::find_all_revisions($view, $model);

		if ( ! $view)
		{
			\Session::set_flash('error', '表示できませんでした');
			$redirect = \Arr::get(static::$locomo, 'main_action');
			$redirect = $redirect ? \Uri::create(\Inflector::ctrl_to_dir($redirect)) : static::$base_url;
			return \Response::redirect($redirect);
		}

		// search_form
		$search_form = \Model_Revision::search_form();

		// assign
		$view->set_global('title', static::$locomo['nicename'].'履歴一覧');
		$view->set_global('base_url', static::$base_url);
		$view->set_global('subject', \Arr::get($model::get_field_by_role('subject'), 'lcm_field'));
		$this->base_assign();
		$this->template->content = $view;
		$this->template->content->set_safe('search_form', $search_form);
	}

	/**
	 * action_each_index_revision()
	 */
	public function action_each_index_revision($id = null)
	{
		is_null($id) and \Response::redirect(static::$base_url);
		// paginated_find
		$options['where'][][]    = array('pk_id', '=', $id);
		$options['where'][][]    = array('model', \Inflector::add_head_backslash($this->model_name));
		$max = max(array_keys($options['where']));
		$options['where'][$max]['or'] = array('model', '\Locomo'.\Inflector::add_head_backslash($this->model_name));
		$options['order_by'][] = array('created_at', 'DESC');

		\Pagination::set_config('uri_segment', 5);
		$items = \Model_Revision::paginated_find($options);
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
		$this->base_assign();
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
			$plain = $model::plain_definition('revision', $obj);
			$val = $plain->build_plain();
		}

		// assign
		$content = \View::forge('revision/view_revision');
		$content->set_safe('comment', $revisions->comment);
		$content->set_safe('user', \Model_Usr::get_display_name($revisions->user_id));
		$content->set_safe('plain', $val);
		$content->set_global('item', $obj);
		$content->set_global('title', '履歴個票');
		$content->set_global('is_revision', true);

		// add_actionset
		$action['urls'][] = \Html::anchor(static::$base_url.'each_index_revision/'.$revisions->pk_id, '履歴一覧へ');
		\Actionset::add_actionset(static::$controller, 'ctrl', $action);

		// view
		$this->base_assign();
		$this->template->content = $content;
	}
}
