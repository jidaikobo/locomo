<?php
namespace Locomo;
class Controller_Crud extends Controller_Base
{
	public $_content_template = null;

	/**
	 * @var array default setting of pagination
	 */
	protected $pagination_config = array(
		'uri_segment' => 3,
		'num_links'   => 5,
		'per_page'    => 20,
		'template' => array(
			'wrapper_start' => '<div class="pagination">',
			'wrapper_end'   => '</div>',
			'active_start'  => '<span class="current">',
			'active_end'    => '</span>',
		),
	);

	/**
	 * action_index_admin()
	 * 管理者用の index
	 * @param $options
	 * @param $model
	 * @param $deleted
	 */
	public function action_index_admin()
	{
		$model = $this->model_name;

		$this->_content_template = $this->_content_template ?: 'index_admin';
		if (\Request::is_hmvc())
		{
			$this->_content_template.= '_widget';
		}
		$content = \View::forge($this->_content_template);

		//$model::paginated_find_use_get_query(false);
		$condition = $model::condition();
		$options = $condition;
		$model::$_conditions = array();

		$content->set('items',  $model::paginated_find($options, $this->pagination_config));

		$content->base_assign();
		$this->template->set_global('title', static::$nicename.'管理一覧');
		$this->template->content = $content;
	}

	/**
	 * action_index()
	 */
	public function action_index()
	{
		$this->_content_template = 'index';
		static::action_index_admin();//$options, $model, $deleted);
		$this->template->set_global('title', static::$nicename.'一覧');
	}

	/**
	 * action_index_yet()
	 * 予約項目
	 * created_at が未来のもの
	 */
	public function action_index_yet()
	{
		$model = $this->model_name;

		if (!isset($model::properties()['created_at']) or !isset($model::properties()['expired_at'])) throw new \HttpNotFoundException;

		$model::$_conditions['where'][] = array('created_at', '>=', date('Y-m-d'));
		$model::$_conditions['where'][] = array('expired_at', '>=', date('Y-m-d'));

		static::action_index_admin();
		$this->template->set_global('title', static::$nicename . '予約項目');
	}

	/**
	 * action_index_expired()
	 */
	public function action_index_expired()
	{
		$model = $this->model_name;
		if (!isset($model::properties()['created_at']) or !isset($model::properties()['expired_at'])) throw new \HttpNotFoundException;

		$model::$_conditions['where'][] = array('created_at', '<=', date('Y-m-d'));
		$model::$_conditions['where'][] = array('expired_at', '<=', date('Y-m-d'));

		static::action_index_admin();
		$this->template->set_global('title', static::$nicename . 'の期限切れ項目');
	}

	/**
	 * action_index_invisible()
	 */
	public function action_index_invisible()
	{
		$model = $this->model_name;
		if (!isset($model::properties()['is_visible'])) throw new \HttpNotFoundException;
		$model::$_conditions['where'][] = array('is_visible', '=', 0);
		static::action_index_admin();
		$this->template->set_global('title', static::$nicename . 'の不可視項目');
	}

	/**
	 * action_index_deleted()
	 */
	public function action_index_deleted()
	{
		$model = $this->model_name;
		if ($model instanceof \Orm\Model_Soft) throw new \HttpNotFoundException;

		$deleted_column = $model::soft_delete_property('deleted_field', 'deletd_at');
		$model::$_conditions['where'][] = array($deleted_column, 'IS NOT', null);

		$model::disable_filter();
		//static::enable_filter();

		static::action_index_admin();
		$this->template->set_global('title', static::$nicename . 'の削除済み項目');
	}

	/*
	 * action_index_all()
	 */
	public function action_index_all()
	{
		$model = $this->model_name;
		if ($model instanceof \Orm\Model_Soft) throw new \HttpNotFoundException;

		$model::disable_filter();
		static::action_index_admin();
		$this->template->set_global('title', static::$nicename . 'の全項目');
	}

	/**
	 * action_view()
	 */
	public function action_view($id = null)
	{
		$model = $this->model_name;

		is_null($id) and \Response::redirect(\Uri::base());

		$authorized_option = $model::authorized_option();

		if ( ! $item = $model::find($id, $authorized_option)):
			\Session::set_flash(
				'error',
				sprintf('%1$s #%2$d は表示できません', self::$nicename, $id)
			);
			throw new \HttpNotFoundException;
			\Response::redirect(\Inflector::ctrl_to_dir(get_called_class()));
		endif;

		//view
		$content = \View::forge($this->_content_template ?: 'view');
		$content->set_safe('plain', $model::plain_definition('view', $item)->build_plain());
		$content->set('item', $item);
		$content->set_global('title', self::$nicename.'閲覧');
		$this->template->content = $content;
		$content->base_assign($item);
	}

	public function action_create() {
		static::action_edit(null);
	}

	public function action_edit($id = null)
	{
		$model = $this->model_name ;
		$content = \View::forge($this->_content_template ?: 'edit');

		if ($id) {
			$obj = $model::find($id, $model::authorized_option(array(), 'edit'));

			if ( ! $obj)
			{
				$page = \Request::forge('content/403')->execute();
				return new \Response($page, 403);
			}
			$title = '#' . $id . ' ' . self::$nicename . '編集';
		} else {
			$obj = $model::forge();
			$title = self::$nicename . '新規作成';
		}
		$form = $model::form_definition('edit', $obj);

		/*
		 * save
		 */
		if (\Input::post()) :
			if (
				$obj->cascade_set(\Input::post(), $form, $repopulate = true) &&
				 \Security::check_token()
			):
				//save
				if ($obj->save(null, true)):
					//success
					\Session::set_flash(
						'success',
						sprintf('%1$sの #%2$d を更新しました', self::$nicename, $obj->id)
					);
					return \Response::redirect(\Uri::create(\Inflector::ctrl_to_dir(get_called_class()).'/edit/'.$obj->id));
				else:
					//save failed
					\Session::set_flash(
						'error',
						sprintf('%1$sの #%2$d を更新できませんでした', self::$nicename, $id)
					);
				endif;
			else:
				//edit view or validation failed of CSRF suspected
				if (\Input::method() == 'POST'):
					$errors = $form->error();
					if ( ! \Security::check_token()) $errors[] = 'ワンタイムトークンが失効しています。送信し直してみてください。';// いつか、エラー番号を与えて詳細を説明する。そのときに二重送信でもこのエラーが出ることを忘れず言う。
					\Session::set_flash('error', $errors);
				endif;
			endif;
		endif;

		//add_actionset - back to index at edit
		$ctrl_url = \Inflector::ctrl_to_dir($this->request->controller);
		$action['urls'][] = \Html::anchor($ctrl_url.DS.'index_admin/','一覧へ');
		$action['order'] = 10;
		\Actionset::add_actionset($this->request->controller, 'ctrl', $action);

		//view
		$this->template->set_global('title', $title);
		$content->set_global('item', $obj, false);
		$content->set_global('form', $form, false);
		$this->template->content = $content;
		$content->base_assign($obj);
	}

	/**
	 * action_delete()
	 * post only
	 * need csrf token
	 */
	public function action_delete($id = null)
	{
		$model = $this->model_name ;
		if ($obj = $model::find($id)) {

			try {
				$obj->delete(null, true);
			}
			catch (\Exception $e) {
				\Session::set_flash(
					'error',
					sprintf('%1$sの #%2$d の削除中にエラーが発生しました', self::$nicename, $id)
				);
			}

			\Session::set_flash(
				'success',
				sprintf('%1$sの #%2$d を削除しました', self::$nicename, $id)
			);

			return \Response::redirect(\Inflector::ctrl_to_dir(get_called_class()) . '/index_deleted');
		}

		\Session::set_flash('error', sprintf('完全削除中にエラーが発生しました'));
		throw new \HttpNotFoundException;
	}

	/**
	 * action_undelete()
	 */
	public function action_undelete($id = null)
	{
		$model = $this->model_name;
		if ($obj = $model::find_deleted($id)) {

			try {
				$obj->undelete();
			}
			catch (\Exception $e) {
				\Session::set_flash('error', sprintf('%1$sの #%2$d の復活中にエラーが発生しました', self::$nicename, $id));
				return \Response::redirect(\Inflector::ctrl_to_dir(get_called_class() . '/index_deleted'));
			}
			\Session::set_flash(
				'success',
				sprintf('%1$sの #%2$d を復活しました', self::$nicename, $id)
			);
			return \Response::redirect(\Inflector::ctrl_to_dir(get_called_class()));
		}

		\Session::set_flash('error', sprintf('項目の復活中にエラーが発生しました'));
		throw new \HttpNotFoundException;
	}

	public function action_purge_confirm ($id = null) {

		$model = $this->model_name;

		// if (!$item = $model::find_deleted($id)) {
		if (!$item = $model::find($id)) {
			\Session::set_flash('error', sprintf('完全削除中にエラーが発生しました'));
			throw new \HttpNotFoundException;
		}

		$content = \View::forge('purge');

		$plain = $model::plain_definition('purge_confirm', $item);
		$content->set_safe('plain', $plain->build_plain());

		$form = \Fieldset::forge('confirm_submit');
		$form->add(\Config::get('security.csrf_token_key'), '', array('type' => 'hidden'))->set_value(\Security::fetch_token());
		$form->add('submit', '', array('type'=>'submit', 'value' => '消去する'))->set_template('<div class="submit">{field}</div>');
		$content->set_safe('form', $form->build(\Inflector::ctrl_to_dir(get_called_class()) . DS . 'purge' . DS . $item->id));

		$this->template->set_global('title', self::$nicename.'消去');
		$this->template->content = $content;
		$content->base_assign($item);
	}

	/**
	 * action_purge()
	 */
	public function action_purge($id = null)
	{
		$model = $this->model_name;
		if (\Auth::is_root()
			and \Input::post()
			and \Security::check_token()
			and $obj = $model::find($id)) {

			try {
				// 現状 Cascading deleteの恩恵を受けられない？ 要実装 
				$obj->purge(null, true);
			}
			catch (\Exception $e) {
				\Session::set_flash('error', sprintf('%1$sの #%2$d の完全削除中にエラーが発生しました', self::$nicename, $id));
				return \Response::redirect(\Inflector::ctrl_to_dir(get_called_class()) . DS .  'index_deleted');
			}

			\Session::set_flash('success', sprintf('%1$sの #%2$d を完全に削除しました', self::$nicename, $id));

			return \Response::redirect(\Inflector::ctrl_to_dir(get_called_class()));
		}

		\Session::set_flash('error', sprintf('項目の完全削除中にエラーが発生しました'));
		throw new \HttpNotFoundException;
	}
}
