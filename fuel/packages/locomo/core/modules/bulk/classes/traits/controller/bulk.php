<?php
namespace Bulk;
trait Traits_Controller_Bulk
{
	/*
	 * @return Fieldset object
	 */
	public function bulk($options = array(), $model = null, $deleted = false, $use_get_query = true, $pagination_config = null, $define_function = null) {

		if (!$model) $model = $this->model_name;
		$action = \Request::main()->action;

		// save から戻ってきた時の処理
		if (\Input::get('ids')) {
			$options['where'] = array(array($model::primary_key()[0], 'IN', \Input::get('ids')));
			$objects = $this->paginated_find($options, $model, 'disabled', false, array());
		// edit create 分岐
		} elseif ($create_field = intval(\Input::get('create'))) { // create
			for ($i = 0; $i < $create_field; $i++) {
				$objects[] = $model::forge();
			}
		} else { //edit 
			$objects = $this->paginated_find($options, $model, $deleted, $use_get_query, $pagination_config);
		}

		if (!$objects) {
			\Session::set_flash('error', '該当が 0 件でした');
			return false;
		}

		$bulk = \Locomo\Bulk::forge();

		$bulk->add_model($objects, $define_function);

		$form = $bulk->build();

		/* deletedも保持 */
		$ids = array();
		foreach ($objects as $object) {
			$ids[] = $object->{$object::primary_key()[0]};
		}



		if (\Input::post() && \Security::check_token()) {
			if ($bulk->save()) {

				// saveした object の保持
				// $ids = array();
				foreach ($objects as $object) {
					$ids[] = $object->{$object::primary_key()[0]};
				}

				$ids = array_unique($ids);
				// 新規を全て空で保存した時の処理
				$judge = array_filter($ids);
				if (empty($judge)) {
					\Session::set_flash('error', '保存対象が 0 件です');
					$url = \Uri::create($this->request->module . '/' . $action, array(), \Input::get());
					return \Response::redirect($url);
				}

				\Session::set_flash('success', self::$nicename . 'を' .  count($ids) . '件保存しました');

				$url = \Uri::create($this->request->module . '/' . $action, array(), array('ids' => $ids));
				return \Response::redirect($url);
			} else {
				\Session::set_flash('error', self::$nicename . 'の保存に失敗しました。エラーメッセージを参照して下さい。');
			}
		}

		$form = $bulk->build();

		return $form;
	}

	

}
