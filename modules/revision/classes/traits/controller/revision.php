<?php
namespace Revision;
trait Traits_Controller_Revision
{
	/**
	 * action_index_revision()
	 */
	public function action_index_revision($model_simple_name = null, $page = 1)
	{
		//vals
		$module = $this->request->module;
		$model = '\\'.ucfirst($module).'\\Model_'.ucfirst($model_simple_name);

		//option - ise \Module\Model_Module::$_option_options['range']
		$opt = false;
		if (\Input::get('opt')):
			if ( ! isset($model::$_option_options[\Input::get('opt')])) die('missing $_option_options.');
			$opt = $model::$_option_options[\Input::get('opt')] ;
		endif;

		//view
		$view = \View::forge(LOCOMOPATH.'modules/revision/views/index_revision.php');
		$view = \Revision\Model_Revision::find_all_revisions($view, $model, $opt);

		if ( ! $view):
			\Session::set_flash('error', '表示できませんでした');
			return \Response::redirect(\Uri::base());
		endif;

		//assign
		if ($opt):
			$view->set_global('title', $opt['nicename'].'履歴一覧');
		else:
			$view->set_global('title', '履歴一覧');
		endif;
		$view->set_global('controller', 'user');
		$view->set_global('subject', $model::get_default_field_name('subject'));
		$view->set_global('model_simple_name', $model_simple_name);
		$view->set_global('opt', \Input::get('opt') ? '?opt='.\Input::get('opt') : '');
		$view->base_assign();
		$this->template->content = $view;
	}

	/**
	 * action_each_index_revision()
	 */
	public function action_each_index_revision($model_simple_name = null, $id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());

		//vals
		$module = $this->request->module;
		$model_str = ucfirst($module).'\\Model_'.ucfirst($model_simple_name);
		$model = '\\'.$model_str;

		//options
		$options['where'][] = array('model', '=', $model_str);
		$options['where'][] = array('pk_id', '=', $id);
		$options['order_by'][] = array('created_at', 'DESC');

		//find pagination_config
		$items = \Revision\Model_Revision::paginated_find($options, array('uri_segment' => 5,));

		//失敗
		if ( ! $items):
			\Session::set_flash('error', '履歴を取得できませんでした');
			return \Response::redirect(\Uri::create($module.'/view/'.$id));
		endif;

		//dataをunserialize（一覧表に編集者とsubjectの変遷を出すため）
		foreach($items as $k => $item):
			$items[$k]->data = unserialize($item->data);
			if ($item->modifier_id == -2)
			{
				$items[$k]->modifier_name = 'root管理者';
			} elseif ($item->modifier_id == -1) {
				$items[$k]->modifier_name = '管理者';
			} else {
				$modifier_name = \User\Model_User::find($item->modifier_id, array('select'=>array('display_name')));
				$modifier_name = $modifier_name ? $modifier_name->display_name : 'deleted user #'.$item->modifier_id;
				$items[$k]->modifier_name = $modifier_name;
			}
		endforeach;

		//add_actionset
		$ctrl_url = \Inflector::ctrl_to_dir($this->request->controller);
		$opt_arg = \Input::get('opt') ? '?opt='.\Input::get('opt') : '';
		if ($opt_arg):
			$action['urls'][] = \Html::anchor($ctrl_url.DS.'edit/'.$id,'編集画面へ');
		endif;
		$action['urls'][] = \Html::anchor($ctrl_url.DS.'index_revision/'.$model_simple_name.DS.$opt_arg,'履歴一覧へ');
		$action['order'] = 10;
		$action['overrides'] = array('base' => array());
		\Actionset::add_actionset($this->request->controller, 'ctrl', $action);

		//view
		$view = \View::forge(LOCOMOPATH.'modules/revision/views/each_index_revision.php');
		$view->set_global('items', $items);
		$view->set_global('controller', $module);
		$view->set_global('title', '履歴');
		$view->set_global('subject', $model::get_default_field_name('subject'));
		$view->set_global('model_simple_name', $model_simple_name);
		$view->set_global('opt', $opt_arg);
		$view->base_assign();
		$this->template->content = $view;
	}


	public function action_view_revision($revision_id = null)
	{

		is_null($revision_id) and \Response::redirect(\Uri::base());

		$model = $this->model_name;

		if ( ! $revisions = \Revision\Model_Revision::find($revision_id)):
			\Session::set_flash('error', '履歴を取得できませんでした');
			return \Response::redirect(\Uri::base());
		endif;

	//	$model = $revisions->model;

		$obj = $model::forge();

		//unserialize
		$data = unserialize($revisions->data);

		$obj->comment = '('.$revisions->operation.') '.$revisions->comment;

		$obj->set($data);
		$plain = $model::plain_definition('revision', $obj);

		//view
		$content = \View::forge(LOCOMOPATH.'modules/revision/views/view_revision.php');
		$content->set_safe('plain', $plain->build_plain());
		$content->set_global('item', $obj);
		$content->set_global('title', '履歴個票');
		$content->set_global('is_revision', true);

		//$module = $this->request->module;
		//add_actionset
		//$opt_arg = \Input::get('opt') ? '?opt='.\Input::get('opt') : '';
		//$module_url = $module ? $module.'/' : '' ;
		//$action['urls'][] = \Html::anchor($module_url.'/each_index_revision/'.$model_simple_name.DS.$revisions->pk_id.$opt_arg, '履歴一覧へ');
		//$action['urls'][] = \Html::anchor($module_url.'/edit/'.$revisions->pk_id, '編集画面へ');
		//$action['order'] = 10;
		//\Actionset::add_actionset($this->request->controller, 'ctrl', $action);
		$content->base_assign();
		$this->template->content = $content;
 
	}
}
