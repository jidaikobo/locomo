<?php
namespace Locomo;
trait Controller_Traits_Bulk
{
	/*
	 * @return Fieldset object
	 */
	public function bulk($options = array(), $model = null, $create = null)
	{
		if (!$model) $model = $this->model_name;
		$action = \Request::main()->action;

		// save から戻ってきた時の処理
		if (\Input::get('ids'))
		{
			$options['where'] = array(array($model::primary_key()[0], 'IN', \Input::get('ids')));
			$pagination_config['per_page'] = count(\Input::get('ids')) * 2;
			$model::disable_filter();
			$objects = $model::paginated_find($options, false);
		// edit create 分岐
		}
		// create
		elseif ($create_field = intval(\Input::get('create')))
		{
			for ($i = 0; $i < $create_field; $i++)
			{
				$objects[] = $model::forge();
			}
		}
		// edit
		elseif ($create)
		{
			for ($i = 0; $i < $create; $i++)
			{
				$objects[] = $model::forge();
			}
		}
		else
		{
			$objects = $model::paginated_find($options);
		}

		if (!$objects)
		{
//			\Session::set_flash('error', '該当が 0 件でした');
			return false;
		}

		$bulk = \Locomo\Bulk::forge();

		$bulk->add_model($objects);

		$form = $bulk->build();

		/* deletedも保持 */
		$ids = array();
		foreach ($objects as $object)
		{
			!is_null($object->{$object::primary_key()[0]}) and $ids[] = $object->{$object::primary_key()[0]};
		}

		if (\Input::post() && \Security::check_token())
		{
			if ($bulk->save())
			{

				// saveした object の保持
				// $ids = array();
				foreach ($objects as $object)
				{
					!is_null($object->{$object::primary_key()[0]}) and $ids[] = $object->{$object::primary_key()[0]};
				}

				$ids = array_unique($ids);

				// 新規を全て空で保存した時の処理
				$judge = array_filter($ids);
				if (empty($judge))
				{
					\Session::set_flash('error', '保存対象が 0 件です');
					$url = \Uri::create($this->base_url.$action, array(), \Input::get());
					return \Response::redirect($url);
				}

				\Session::set_flash('success', self::$nicename . 'への変更を' . count($ids) . '件保存しました');

				$url = \Uri::create($this->base_url.$action, array(), array('ids' => $ids));
				return \Response::redirect($url);
			}
			else
			{
				\Session::set_flash('error', self::$nicename . 'の保存に失敗しました。エラーメッセージを参照して下さい。');
			}
		}

		$form = $bulk->build();

		return $form;
	}
}
