<?php
namespace Revision;
trait Traits_Controller_Revision
{
	/**
	 * action_index_revision()
	 */
	public function action_index_revision($page = 1)
	{
		// find_all_revisions
		$model = $this->model_name;
		$view = \View::forge(LOCOMOPATH.'modules/revision/views/index_revision.php');
		$view = \Revision\Model_Revision::find_all_revisions($view, $model);
		if ( ! $view)
		{
			\Session::set_flash('error', '表示できませんでした');
			$redirect = \Arr::get(static::$locomo, 'admin_home');
			$redirect = $redirect ? \Uri::create(\Inflector::ctrl_to_dir($redirect)) : $this->base_url;
			return \Response::redirect($redirect);
		}

		// assign
		$view->set_global('title', static::$locomo['nicename'].'履歴一覧');
		$view->set_global('base_url', $this->base_url);
		$view->set_global('subject', $model::get_default_field_name('subject'));
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_each_index_revision()
	 */
	public function action_each_index_revision($id = null)
	{
		is_null($id) and \Response::redirect($this->base_url);

		// paginated_find
		$options['where'][]    = array('model', '=', \Inflector::add_head_backslash($this->model_name));
		$options['where'][]    = array('pk_id', '=', $id);
		$options['order_by'][] = array('created_at', 'DESC');
		\Pagination::set_config('uri_segment', 5);
		$items = \Revision\Model_Revision::paginated_find($options);
		if ( ! $items):
			\Session::set_flash('error', '履歴を取得できませんでした');
			$ret = method_exists(__CLASS__, 'action_view') ? $this->base_url.'view/'.$id : $this->base_url;
			return \Response::redirect($ret);
		endif;

		// unserialize data - to display usernames
		foreach($items as $k => $item):
			$items[$k]->data = unserialize($item->data);
			// root
			if ($item->user_id == -2)
			{
				$items[$k]->modifier_name = 'root管理者';
			}
			// admin
			elseif ($item->user_id == -1)
			{
				$items[$k]->modifier_name = '管理者';
			}
			// users
			else
			{
				$modifier_name = \User\Model_User::find($item->user_id, array('select'=>array('display_name')));
				$modifier_name = $modifier_name ? $modifier_name->display_name : 'deleted user #'.$item->user_id;
				$items[$k]->modifier_name = $modifier_name;
			}
		endforeach;

		// add_actionset
		$action['urls'][] = \Html::anchor($this->base_url.'index_revision/','履歴一覧へ');
		$action['order'] = 10;
		$action['overrides']['base'] = array(
//			\Html::anchor($this->base_url.'view/'.$id,'閲覧'),
//			\Html::anchor($this->base_url.'edit/'.$id,'編集')
		);
		\Actionset::add_actionset($this->request->controller, 'ctrl', $action);

		// subject field
		$model = $this->model_name;
		$subject = $model::get_default_field_name('subject');
		if(empty($subject)) throw new \OutOfBoundsException($model.' doesn\'t have public static $_subject_field_name');

		// view
		$view = \View::forge(LOCOMOPATH.'modules/revision/views/each_index_revision.php');
		$view->set_global('items', $items);
		$view->set_global('base_url', $this->base_url);
		$view->set_global('title', '履歴一覧');
		$view->set_global('subject', $subject);
		$view->base_assign();
		$this->template->content = $view;
	}


	public function action_view_revision($revision_id = null)
	{
		is_null($revision_id) and \Response::redirect($this->base_url);

		if ( ! $revisions = \Revision\Model_Revision::find($revision_id))
		{
			\Session::set_flash('error', '履歴を取得できませんでした');
			return \Response::redirect($this->base_url);
		}

		// prepare data
		$model = $this->model_name;
		$data = unserialize($revisions->data);
		$obj = $model::forge();
		$obj->comment = '('.$revisions->operation.') '.$revisions->comment;
		$obj->set($data);
		$plain = $model::plain_definition('revision', $obj);

		// assign
		$content = \View::forge(LOCOMOPATH.'modules/revision/views/view_revision.php');
		$content->set_safe('plain', $plain->build_plain());
		$content->set_global('item', $obj);
		$content->set_global('title', '履歴個票');
		$content->set_global('is_revision', true);

		// add_actionset
		$opt_arg = \Input::get('opt') ? '?opt='.\Input::get('opt') : '';
		$action['urls'][] = \Html::anchor($this->base_url.'each_index_revision/'.$revisions->pk_id, '履歴一覧へ');
		$action['order'] = 10;
		$action['overrides']['base'] = array(
//			\Html::anchor($this->base_url.'view/'.$revisions->pk_id,'閲覧'),
//			\Html::anchor($this->base_url.'edit/'.$revisions->pk_id,'編集')
		);
		\Actionset::add_actionset($this->request->controller, 'ctrl', $action);

		// view
		$content->base_assign();
		$this->template->content = $content;
	}
}
