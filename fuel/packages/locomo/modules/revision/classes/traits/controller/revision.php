<?php
namespace Revision;
trait Traits_Controller_Revision
{
	/**
	 * action_index_revision()
	 */
	public function action_index_revision($model_simple_name, $page = 1)
	{
		//vals
		$module = $this->request->module;
		$model = '\\'.ucfirst($module).'\\Model_'.ucfirst($model_simple_name);

		//option - ise \Module\Model_Module::$_option_options['range']
		$opt = false;
		if(\Input::get('opt')):
			if( ! isset($model::$_option_options[\Input::get('opt')])) die('missing $_option_options.');
			$opt = $model::$_option_options[\Input::get('opt')] ;
		endif;

		//view
		$view = \View::forge(LOCOMOPATH.'modules/revision/views/index_revision.php');
		$view = \Revision\Model_Revision::find_all_revisions($view, $model, $opt);

		if( ! $view):
			\Session::set_flash('error', '表示できませんでした');
			return \Response::redirect(\Uri::base());
		endif;

		//assign
		if($opt):
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
	public function action_each_index_revision($model_simple_name, $id = null)
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
		$ctrl_url = \Inflector::to_dir($this->request->controller);
		$opt_arg = \Input::get('opt') ? '?opt='.\Input::get('opt') : '';
		if($opt_arg):
			$action['urls'][] = \Html::anchor($module.DS.$ctrl_url.DS.'edit/'.$id,'編集画面へ');
		endif;
		$action['urls'][] = \Html::anchor($module.DS.$ctrl_url.DS.'index_revision/'.$model_simple_name.DS.$opt_arg,'履歴一覧へ');
		$action['order'] = 10;
		$action['overrides'] = array('base' => array());
		\Actionset::add_actionset($this->request->controller, $this->request->module, 'ctrl', $action);

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

	/**
	 * action_view_revision()
	 */
	public function action_view_revision($model_simple_name, $id = null)
	{
		is_null($id) and \Response::redirect(\Uri::base());
		$model = \Revision\Model_Revision::forge();
		$module = $this->request->module;

		if ( ! $revisions = $model::find($id)):
			\Session::set_flash('error', '履歴を取得できませんでした');
			return \Response::redirect(\Uri::base());
		endif;

		//unserialize
		$data = unserialize($revisions->data);
		$data->comment = '('.$revisions->operation.') '.$revisions->comment;

		//model
		$original_model = '\\'.ucfirst($module).'\\Model_'.ucfirst($model_simple_name);
		$pk = $original_model::get_primary_keys('first');

		//option - ise \Module\Model_Module::$_option_options['range']
		$opt = false;
		$opt_arg = '';
		if(\Input::get('opt')):
			if( ! isset($original_model::$_option_options[\Input::get('opt')])) die('missing $_option_options.');
			$opt = $original_model::$_option_options[\Input::get('opt')] ;
			$opt_arg = '?opt='.\Input::get('opt');
		endif;

		//form definition
		$data->$pk   = $revisions->pk_id;
		$template = 'edit';
		if(isset($opt['form_definition'])):
			$form = $original_model::{$opt['form_definition']}('revision', $data);
		else:
			//普通のform_definition
			$form = $original_model::form_definition('revision', $data);
		endif;

		//template
		if(isset($opt['template']) && ! empty($opt['template'])):
			//指定テンプレート
			$template = $opt['template'];
		elseif(isset($opt['template']) && empty($opt['template'])):
			//bulk
			$template = LOCOMOPATH.'modules/bulk/views/bulk.php';
		endif;
		
		//view
		$view = \View::forge($template);
		$view->set_global('form', $form, false);
		$view->set_global('item', $data);
		$view->set_global('title', '履歴個票');
		$view->set_global('is_revision', true);

		//add_actionset
		$opt_arg = \Input::get('opt') ? '?opt='.\Input::get('opt') : '';
		$module_url = $module ? $module.'/' : '' ;
		$action['urls'][] = \Html::anchor($module_url.'/each_index_revision/'.$model_simple_name.DS.$revisions->pk_id.$opt_arg, '履歴一覧へ');
		$action['urls'][] = \Html::anchor($module_url.'/edit/'.$revisions->pk_id, '編集画面へ');
		$action['order'] = 10;
		\Actionset::add_actionset($this->request->controller, $module, 'ctrl', $action);
		$view->base_assign();
		$this->template->content = $view;
	}

}