<?php
namespace Bulk;
trait Traits_Controller_Bulk
{
	public function action_bulk() {


		$view = \View::forge(PKGCOREPATH . 'modules/bulk/views/bulk.php');
		$form = $this->bulk($view, null, array());

		$view->set_global('title', 'バルク品');
		$view->set_global('form', $form, false);


		//add_actionset
		$action = array(
			'url' => 'user/',
			'menu_str' => '編集画面に戻る',
		);
//		\Actionset::add_actionset($this->request->module, 'ctrl', 'back', $action);
//		$array = ['menu_str'=>'戻る', 'url' =>'user/'];
		\Actionset::set_actionset('user', 'ctrl', 'back', $action);

		return \Response::forge(\ViewModel::forge($this->request->module, 'view', null, $view));

	}

	/*
	 *
	 */
	public function bulk($view = null, $model = null, $options = array(), $define_function = null) {

		is_null($view) and die('view is required');
		if (!$model) $model = $this->model_name;
		$action = \Request::main()->action;

		if (\Input::get()) {
			if (\Input::get('orders')) {
				$orders = array();
				foreach (\Input::get('orders') as $k => $v) {
					$orders[$k] = $v;
				}
				$options['order_by'] = $orders;
			}
			if (\Input::get('searches')) {
				foreach (\Input::get('searches') as $k => $v) {
					$options['where'][] = array($k, '=', $v);
				}
			}
			if (\Input::get('likes')) {
				$likes = array();
				foreach (\Input::get('likes') as $k => $v) {
					$options['where'][] = array($k, 'LIKE', '%' . $v . '%');
				}
			}
		}

		// save から戻ってきた時の処理
		//if (\Input::get('ids')) var_dump( \Input::get('ids')); die();
		if (\Input::get('ids')) $options['where'] = array(array($model::primary_key()[0], 'IN', \Input::get('ids')));

		// 件数取得
		$count = $model::count($options);
		$view->set('hit', $count);

		// pagination
		$pagination_config = $this->pagination_config;
		$pagination_config['uri_segment'] = 'page';
		$pagination_config['total_items'] = $count;
		$pagination_config['pagination_url'] = \Uri::create('/'.$this->request->module.'/'.$action.'/', array(), \Input::get());
		\Pagination::set_config($pagination_config);
		$options['offset'] = \Input::get('offset') ?: \Pagination::get('offset');
		$options['limit'] = \Input::get('limit') ?: $pagination_config['per_page'];
		$view->set_safe('pagination', \Pagination::create_links());

		// edit create 分岐
		$create_field = intval(\Input::get('create'));
		if (!$create_field) {
			$objects = $model::find('all', $options);
		} else { // create
			for ($i = 0; $i < $create_field; $i++) {
				$objects[] = $model::forge();
			}
		}

		//var_dump($objects);

		if (!$objects) {
			\Session::set_flash('error', '該当が 0 件でした');
			return false;
		}

		$bulk = \Locomo\Bulk::forge();

		$bulk->add_model($objects, $define_function);

		$form = $bulk->build();

		if (\Input::post() && \Security::check_token()) {
			if ($bulk->save()) {

				// saveした object の保持
				$ids = array();
				foreach ($objects as $object) {
					$ids[] = $object->{$object::primary_key()[0]};
				}

				// var_dump($ids); die();
				// 新規を全て空で保存した時の処理
				$judge = array_filter($ids);
				if (empty($judge)) {
					\Session::set_flash('error', '保存対象が 0 件です');
					$url = \Uri::create($this->request->module . '/' . $action, array(), \Input::get());
					return \Response::redirect($url);
				}


				\Session::set_flash(
					'success',
					sprintf($this->messages['edit_success'], self::$nicename, count($ids) . '件')
				);


				$url = \Uri::create($this->request->module . '/' . $action, array(), array('ids' => $ids));
				return \Response::redirect($url);
			} else {
				\Session::set_flash(
					'error',
					sprintf($this->messages['edit_error'], self::$nicename, '')
				);
			}
		}

		$form = $bulk->build();

		return $form;
	}

}
