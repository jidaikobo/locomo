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

				$res = \Upload::save();

				// upload
				$files = \Upload::get_files();

				foreach ($files as $file)
				{
					$data = file_get_contents($file['saved_to'].$file['saved_as']);
					// $data = mb_convert_encoding($data, 'UTF-8', 'SJISwin');
					mb_convert_variables('UTF-8', 'SJIS', $data);
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
							$field_name = reset($fields);

							$value = isset($row_data[$cnt]) ? $row_data[$cnt] : null;

							// '.' があったらリレーション
							if (strpos($field_name, '.') !== false)
							{
								$related_name = substr($field_name, 0, strpos($field_name, '.'));
								$related_field = substr($field_name, strpos($field_name, '.') +1);
								$arr[$related_name][$related_field] = $value;
							}
							else
							{
								$arr[$field_name] = $value;
							}

							$cnt++;
						}

						$save_result = static::save_object($arr, $format);
					}
				}
				\Session::set_flash('success' , 'インポートが完了いたしました');
			}
			else
			{
				\Session::set_flash('error' , 'ファイルのアップロードに失敗いたしました');
			}

		}

		$content = \Presenter::forge($this->_content_template ?: static::$dir.'import');
		$form = $content::form();

		$content->get_view()->set_global('item', $format, false);
		$content->get_view()->set_global('form', $form, false);
		$this->template->set_safe('content', $content);
		$this->template->set_global('title', 'インポート');
	}

	/**
	 * save_object()
	 * Override 用
	 * 複雑なリレーションに対応する場合は Override する
	 */
	protected function save_object($arr, $format)
	{
		$model = $format->model;
		return $model::forge($arr)->save();
	}
}
