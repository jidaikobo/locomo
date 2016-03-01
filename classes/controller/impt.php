<?php
/*
 * Base のコントローラー 直接は呼ばない
 */
namespace Locomo;
class Controller_Impt extends \Controller_Base
{
	/*
	 * before()
	 */
	public function before()
	{
		parent::before();
		if (!$this->model_name)
		{
			$this->model_name = '';
		}
	}

	/*
	 * action_index_admin
	 */
	public function action_index_admin()
	{
		if (!$this->_content_template) $this->_content_template = 'impt/index_admin';

		parent::index_admin();

		if ($this->output_url) $this->template->content->output_url = \Uri::create($this->output_url);
	}

	/**
	 * action_import()
	 */
	public function action_import($id = null)
	{
		if (!$this->_content_template) $this->_content_template = 'impt/import';

		$model = $this->model_name ?: '\Locomo\Model_Frmt';

		$format = $model::find($id);


		if (\Input::post() && \Input::file())
		{
			$upload_path = APPPATH.'/tmp/import/';

			if ( ! is_dir($upload_path))
			{
				mkdir($upload_path, 0777);
			}
			$config = array(
				'path' => $upload_path,
				'randomize' => true,
				'ext_whitelist' => array('csv'),
			);
			\Upload::process($config);

			if (\Upload::is_valid())
			{

				// die();
				$res = \Upload::save();

				// upload
				$files = \Upload::get_files();

				// Import
				$is_relation = false;
				$insert_model = $format->model;

				foreach ($files as $file)
				{
					$data = file_get_contents($file['saved_to'].$file['saved_as']);
					// $data = mb_convert_encoding($data, 'UTF-8', 'SJISwin');
					mb_convert_variables('UTF-8', 'SJIS', $data);
					// インポートする物がこのシステムからとは限らないので、
					$data = \Format::forge($data, 'csv', false)->to_array();

					$data = static::convert_objects($data, $format);

					if (\Input::post('ignore_one_line')) array_shift($data);

					$belongs_to = array();
					$belongs_to_saved = array();
					foreach ($data as $row_data)
					{
						$arr = array();
						$cnt = 0;
						foreach ($format->element as $element)
						{
							$fields = explode('}', trim(str_replace('{', '}' , $element->txt), '}') );
							if (count($fields) !== 1) continue; // todo throw error
							$field_name = reset($fields);

							// リレーションの処理
							if (strpos($field_name, '.') !== false)
							{
								$related_name = substr($field_name, 0, strpos($field_name, '.'));
								$related_field = substr($field_name, strpos($field_name, '.') +1);
								if ($insert_model::relations($related_name) &&
									(get_class($insert_model::relations($related_name)) == 'Orm\BelongsTo' ||
									get_class($insert_model::relations($related_name)) == 'Orm\HasOne')
								)
								{
									$value = isset($row_data[$cnt]) ? $row_data[$cnt] : null;
									$belongs_to[$related_name][$related_field] = $value;
								}
							}

							else
							{
								$value = isset($row_data[$cnt]) ? $row_data[$cnt] : null;

								$arr[$field_name] = $value;
							}

							$cnt++;
						}

						foreach ($belongs_to as $related_name => $bt)
						{
							$relate_model = $insert_model::relations($related_name);
							$relate_model_name = $relate_model->model_to;
							$key_from = $relate_model->key_from[0];
							$key_to = $relate_model->key_to[0];

							if (isset($belongs_to_saved[$related_name]) &&
								$format::format_import_matcher($belongs_to_saved[$related_name], $bt))
							{
								$arr[$key_from] = $relate_save_item->{$key_to};
								continue;
							}

							$relate_save_item = $relate_model_name::forge($bt);
							$relate_save_item->save();

							$belongs_to_saved[$related_name] = $bt;

							$key_from = $relate_model->key_from[0];
							$key_to = $relate_model->key_to[0];
							$arr[$key_from] = $relate_save_item->{$key_to};
						}

						$insert_model::forge($arr)->save();
					}
				}
			}

		}

		$content = \Presenter::forge($this->_content_template ?: static::$dir.'import');
		$form = $content::form();

		$content->get_view()->set_global('item', $format, false);
		$content->get_view()->set_global('form', $form, false);
		$this->template->set_safe('content', $content);


		$this->template->set_global('title', 'インポート');
	}


	protected static function create_insert_column($format)
	{
		foreach ($format->element as $element)
		{
		}
	}

	public static function insert_column_flatten($excel_fields, $format)
	{
		$ret_arr = array();
		foreach($excel_fields as $k => $v)
		{
			if (is_array($v))
			{
				foreach ($v as $kk => $vv)
				{
					$ret_arr[$kk] = $vv;
				}
			}
			else
			{
				$ret_arr[$k] = $v;
			}
		}

		return $ret_arr;
	}


	/*
	 * Override 用
	 * フィールドの出力を変えたい時などに使う
	 */
	protected static function convert_objects($objects, $format)
	{
		return $objects;
	}


}
