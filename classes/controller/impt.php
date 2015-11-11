<?php
/*
 * Base のコントローラー 直接は呼ばない
 */
namespace Locomo;
class Controller_Impt extends \Locomo\Controller_Base
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

				var_dump($insert_model::relations());
				die();

				foreach ($files as $file)
				{
					$data = file_get_contents($file['saved_to'].$file['saved_as']);
					mb_convert_variables('UTF-8', null, $data);
					// インポートする物がこのシステムからとは限らないので、
					$data = \Format::forge($data, 'csv', false)->to_array();

					if (\Input::post('ignore_one_line')) array_shift($data);

					foreach ($data as $row_data)
					{
						$arr = array();
						$cnt = 0;
						foreach ($format->element as $element)
						{
							$fields = explode('}', trim(str_replace('{', '}' , $element->txt), '}') );
							if (count($fields) !== 1) continue; // todo throw error
							$field = reset($fields);
							$value = isset($row_data[$cnt]) ? $row_data[$cnt] : null;

							$arr[$field] = $value;

							$cnt++;
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

}
