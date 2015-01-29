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
	 * action_admin()
	 * this action is placeholder for actionset. Don't delete.
	 */
	public function action_admin()
	{
	}

	/**
	 * index_core()
	 */
	protected function index_core()
	{
		$model = $this->model_name;

		$dir = substr(strtolower(\Inflector::denamespace(\Request::active()->controller)), 11).DS;
		if (!$this->_content_template) {
			if (!\Request::is_hmvc()) {
				$this->_content_template = $dir.'index_admin';
			} else {
				$this->_content_template = $dir.'index_admin_widget';
			}
		}
		$content = \View::forge($this->_content_template);

		// hmvc gives args by \Request::forge()->execute($args)
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
			$options = $model::condition();
		}
		$model::$_conditions = array();
//		$options = array('expired_at', '>', date('Y-m-d H:i:s'));

		$content->set('items',  $model::paginated_find($options));

		$this->template->content = $content;
	}

	/**
	 * index_admin()
	 */
	protected function index_admin()
	{
		$model = $this->model_name;

		if (isset($model::properties()['created_at']))
		{
			$model::$_conditions['where'][] = array('created_at', '<=', date('Y-m-d H:i:s'));
		}
		if (isset($model::properties()['expired_at']))
		{
			$model::$_conditions['where'][] = array('expired_at', 'is', null);
		}
		if (isset($model::properties()['is_visible']))
		{
			$model::$_conditions['where'][] = array('is_visible', '=', true);
		}

		static::index_core();//$options, $model, $deleted);
		$this->template->set_global('title', static::$nicename.'管理一覧');
	}

	/**
	 * index()
	 */
	protected function index()
	{
		$this->_content_template = 'index';
		static::index_core();//$options, $model, $deleted);
		$this->template->set_global('title', static::$nicename.'一覧');
	}

	/**
	 * index_yet()
	 * 予約項目
	 * created_at が未来のもの
	 */
	protected function index_yet()
	{
		$model = $this->model_name;

		if (!isset($model::properties()['created_at']) or !isset($model::properties()['expired_at'])) throw new \HttpNotFoundException;

		$model::$_conditions['where'][] = array('created_at', '>=', date('Y-m-d H:i:s'));
		$model::$_conditions['where'][] = array('expired_at', '>=', date('Y-m-d H:i:s'));

		static::index_core();
		$this->template->set_global('title', static::$nicename . '予約項目');
	}

	/**
	 * index_expired()
	 */
	protected function index_expired()
	{
		$model = $this->model_name;
		if (!isset($model::properties()['created_at']) or !isset($model::properties()['expired_at'])) throw new \HttpNotFoundException;

//		$model::$_conditions['where'][] = array('created_at', '<=', date('Y-m-d'));
		$model::$_conditions['where'][] = array('expired_at', '<', date('Y-m-d H:i:s'));

		static::index_core();
		$this->template->set_global('title', static::$nicename . 'の期限切れ項目');
	}

	/**
	 * index_invisible()
	 */
	protected function index_invisible()
	{
		$model = $this->model_name;
		if (!isset($model::properties()['is_visible'])) throw new \HttpNotFoundException;
		$model::$_conditions['where'][] = array('is_visible', '=', 0);
		static::index_core();
		$this->template->set_global('title', static::$nicename . 'の不可視項目');
	}

	/**
	 * index_deleted()
	 */
	protected function index_deleted()
	{
		$model = $this->model_name;
		if ($model instanceof \Orm\Model_Soft) throw new \HttpNotFoundException;

		$deleted_column = $model::soft_delete_property('deleted_field', 'deletd_at');
		$model::$_conditions['where'][] = array($deleted_column, 'IS NOT', null);

		$model::disable_filter();
		//static::enable_filter();

		static::index_core();
		$this->template->set_global('title', static::$nicename . 'の削除済み項目');
	}

	/*
	 * index_all()
	 */
	protected function index_all()
	{
		$model = $this->model_name;
		if ($model instanceof \Orm\Model_Soft) throw new \HttpNotFoundException;
		$model::disable_filter();
		static::index_core();
		$this->template->set_global('title', static::$nicename . 'の全項目');
	}

	/**
	 * view()
	 */
	protected function view($id = null)
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
		// $dir = substr(strtolower(\Inflector::denamespace(\Request::active()->controller)), 11).DS;
		// $content = \View::forge($this->_content_template ?: $dir.'view');

		if ($this->_content_template) {
			$content = \View::forge($this->_content_template);
		} else {
			$tmp = substr(strtolower(\Inflector::denamespace(\Request::active()->controller)), 11).DS.'/view';
			// var_dump(\Finder::search('views', $tmp)); die();
			$content = \View::forge(
				\Finder::search('views', $tmp) ? $tmp : 'defaults/view'
			);
		}

		$content->set_safe('plain', $model::plain_definition('view', $item)->build_plain());
		$content->set('item', $item);
		$content->set_global('title', self::$nicename.'閲覧');
		$this->template->content = $content;
		static::set_object($item);
	}

	/**
	 * create()
	 */
	protected function create($id = null, $redirect = null)
	{
		$locomo_path = \Inflector::ctrl_to_dir(\Request::main()->controller.DS.\Request::main()->action);
		$redirect = $redirect ?: str_replace('create', 'edit', $locomo_path);
		static::edit($id, $redirect);
	}

	/*
	 * edit()
	 */
	protected function edit($id = null, $redirect = null)
	{
		// vals
		$model = $this->model_name ;

		if ($this->_content_template) {
			$content = \View::forge($this->_content_template);
		} else {
			$tmp = substr(strtolower(\Inflector::denamespace(\Request::active()->controller)), 11).DS.'/edit';
			// var_dump(\Finder::search('views', $tmp)); die();
			$content = \View::forge(
				\Finder::search('views', $tmp) ? $tmp : 'defaults/edit'
			);
		}

		if ($id)
		{
			$obj = $model::find($id, $model::authorized_option(array(), 'edit'));
			// not found
			if ( ! $obj)
			{
				$page = \Request::forge('sys/403')->execute();
				$this->template->set_safe('content', $page);
				return new \Response($page, 403);
			}
			$title = '#' . $id . ' ' . self::$nicename . '編集';
		}
		else
		{
			$obj = $model::forge();
			$title = self::$nicename . '新規作成';
		}
		$form = $model::form_definition('edit', $obj);

		// save
		if (\Input::post())
		{
			if (
				($check_token = \Security::check_token()) &&
				$obj->cascade_set(\Input::post(), $form, $repopulate = true)
			)
			{
				//save
				if ($obj->save(null, true))
				{
					//success
					\Session::set_flash(
						'success',
						sprintf('%1$sの #%2$d を更新しました', self::$nicename, $obj->id)
					);

					// redirect
					$locomo_path = \Inflector::ctrl_to_dir(\Request::main()->controller.DS.\Request::main()->action);
					$redirect = $redirect ? $redirect.DS.$obj->id : $locomo_path.DS.$obj->id;
					static::$redirect = $redirect;

					return $obj;
				}
				else
				{
					//save failed
					\Session::set_flash(
						'error',
						sprintf('%1$sの #%2$d を更新できませんでした', self::$nicename, $id)
					);
				}
			}
			else
			{
				//edit view or validation failed of CSRF suspected
				if (\Input::method() == 'POST')
				{
					$errors = $form->error();
					// いつか、エラー番号を与えて詳細を説明する。そのときに二重送信でもこのエラーが出ることを忘れず言う。
					if ( ! $check_token) $errors[] = 'ワンタイムトークンが失効しています。送信し直してみてください。';
					\Session::set_flash('error', $errors);
				}
			}
		}

		//view
		$this->template->set_global('title', $title);
		$content->set_global('item', $obj, false);
		$content->set_global('form', $form, false);
		$this->template->content = $content;
		static::set_object($obj);

		return false;
	}

	/**
	 * delete()
	 * post only
	 * need csrf token
	 */
	protected function delete($id = null)
	{
		$model = $this->model_name ;
		if ($obj = $model::find($id))
		{
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

		\Session::set_flash('error', sprintf('削除中にエラーが発生しました'));
		throw new \HttpNotFoundException;
	}

	/**
	 * undelete()
	 */
	protected function undelete($id = null)
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
			return \Response::redirect(\Inflector::ctrl_to_dir(get_called_class(). '/index_admin'));
		}

		\Session::set_flash('error', sprintf('項目の復活中にエラーが発生しました'));
		throw new \HttpNotFoundException;
	}

	/**
	 * purge_confirm()
	 */
	protected function purge_confirm ($id = null)
	{

		$model = $this->model_name;

		// if (!$item = $model::find_deleted($id)) {
		if (!$item = $model::find($id)) {
			\Session::set_flash('error', sprintf('完全削除中にエラーが発生しました'));
			throw new \HttpNotFoundException;
		}

		$content = \View::forge($this->_content_template ?: 'purge');

		$plain = $model::plain_definition('purge_confirm', $item);
		$content->set_safe('plain', $plain->build_plain());

		$form = \Fieldset::forge('confirm_submit');
		$form->add(\Config::get('security.csrf_token_key'), '', array('type' => 'hidden'))->set_value(\Security::fetch_token());
		$form->add('submit', '', array('type'=>'submit', 'value' => '消去する'))->set_template('<div class="submit">{field}</div>');
		$content->set_safe('form', $form->build(\Inflector::ctrl_to_dir(get_called_class()) . DS . 'purge' . DS . $item->id));

		$this->template->set_global('title', self::$nicename.'消去');
		$this->template->content = $content;
		static::set_object($item);
	}

	/**
	 * purge()
	 */
	protected function purge($id = null)
	{
		$model = $this->model_name;

		$model::disable_filter();
		if (\Auth::is_root()
			//and \Input::post()
			//and \Security::check_token()
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

			return \Response::redirect(\Inflector::ctrl_to_dir(get_called_class() . '/index_deleted'));
		}

		\Session::set_flash('error', sprintf('項目の完全削除中にエラーが発生しました'));
		throw new \HttpNotFoundException;
	}
}
