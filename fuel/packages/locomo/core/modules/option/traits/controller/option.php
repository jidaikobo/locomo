<?php
namespace Option;
trait Controller_Option
{
	/**
	 * action_option()
	 */
	public function action_option($optname = null)
	{
		is_null($optname) and die();
		$model = substr(get_called_class(), 0, strrpos(get_called_class(), '\\')).'\\Model_'.ucfirst($optname);
		$opt = $model::get_option_options($optname);
		$order = @$opt['order_field'] ? array('order_by' => array(array($opt['order_field'],'ASC'))) : array() ;
		$items = $model::find('all', $order);

		//view
		if (\Input::method() == 'POST' && \Security::check_token()):
			//新規追加
			if(\Input::post('mode') == 'add'):
				//nameは必須
				if( ! \Input::post('name')):
					$err = array('項目名は必須です');
					\Session::set_flash('error', $err);
				else:
					//add_option()
					$model::add_option(\Input::post());
					\Session::set_flash('success', '項目を新規追加しました');
				endif;
			//削除
			elseif(\Input::post('delete')):
				$id = key(\Input::post('delete'));
				$model::delete_option($id);
				\Session::set_flash('success', '項目を削除しました');
			//編集
			elseif(\Input::post('mode') == 'edit'):
				$err = array();
				foreach(\Input::post('items') as $item):
					if(empty($item['name'])):
						$err = array('項目名が空の値については更新できませんでした');
					else:
						$model::update_option($item);
					endif;
				endforeach;
				//編集のメッセージ
				if($err):
					\Session::set_flash('error', $err);
				else:
					\Session::set_flash('success', '項目を編集しました');
				endif;
			endif;
			//編集あるいは新規追加が終わったらリビジョンをアップデート
			$items = $model::find('all');

			//find()したものをそのままserialize()するとunserialize()したときに__PHP_Incomplete_Classになってしまうので、いったん別のobjectにする。
			$tmps = array();
			$n = 0;
			foreach($items as $item):
				$tmps[$n] = (object) array();
				foreach($model::properties() as $property => $v):
					$tmps[$n]->{$property} = $item->{$property};
				endforeach;
				$n++;
			endforeach;

			$args = array();
			$args['model']       = $optname;
			$args['pk_id']       = 0;
			$args['data']        = serialize($tmps);
			$args['comment']     = \Input::post('revision_comment') ?: '';
			$args['created_at']  = date('Y-m-d H:i:s');
			$args['modifier_id'] = \User\Controller_User::$userinfo['user_id'];
			$rev_model = \Revision\Model_Revision::forge($args);
			$rev_model->insert_revision();

			return \Response::redirect(\Uri::create($this->request->module.'/option/'.$optname));
		endif;

		//view
		$view = \View::forge('option_'.$optname);
		$view->set_global('items', $items);
		$view->set_global('title', $opt['nicename']);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_option_revisions()
	 */
	public function action_option_revisions($optname = null)
	{
		is_null($optname) and die();
		$model = \Revision\Model_Revision::forge();

		if ( ! $revisions = $model::find_options_revisions($optname)):
			\Session::set_flash('error','履歴がありません');
			return \Response::redirect(\Uri::base());
		endif;

		//view
		$view = \View::forge(\Util::fetch_tpl('/revision/views/index_option.php'));

		$view->set_global('items', $revisions);
		$view->set_global('optname', $optname);
		$view->set_global('title', 'オプションの編集履歴');

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}

	/**
	 * action_option_revision()
	 */
	public function action_option_revision($optname = null, $datetime = null)
	{
		if(is_null($optname) || is_null($datetime)) die();
		$model = \Revision\Model_Revision::forge();

		if ( ! $revisions = $model::find_options_revision($optname, $datetime)):
			\Session::set_flash('error','履歴がありません');
			return \Response::redirect(\Uri::base());
		endif;

		//unserialize
		$data = (object) array();
		$data->comment = isset($revisions->comment) ? $revisions->comment : '' ;

		//unserialize()した値をそのまま渡すとなぜか配列が倍に増えるので、ここで対応
		$vals = (object) unserialize($revisions->data);
		$retvals = (object) array();
		foreach($vals as $k => $val):
			$data->$k = (object) $val;
		endforeach;

		//view
		$view = \View::forge('option_'.$optname);
		$view->set_global('optname', $optname);
		$view->set_global('items', $data);
		$view->set_global('title', '編集履歴');
		$view->set_global('is_revision', true);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));
	}
}
