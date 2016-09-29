<?php
namespace Locomo;
trait Controller_Traits_Impt
{
	/**
	 * インポートの本体
	 */
	protected function import($format)
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

			$errors = array();

			foreach ($files as $file)
			{


				$file = new \SplFileObject($file['saved_to'].$file['saved_as']); 
				$file->setFlags(\SplFileObject::READ_CSV);


				$row_cnt = 0;
				// ファイル内のデータループ
				foreach ($file as $key => $row_data)
				{
					$row_cnt++;
					if (\Input::post('ignore_one_line') && $row_cnt < 2) continue;

					if (is_null($row_data[0])) continue; // 空行 (行末の改行など)
					mb_convert_variables('UTF-8', 'SJIS', $row_data);
					$arr = array();
					$cnt = 0;

					foreach ($format->element as $element)
					{
						$fields = explode('}', trim(str_replace('{', '}' , $element->txt), '}') );
						if (count($fields) !== 1) continue; // todo throw error
						$field_name = reset($fields);

						$value = isset($row_data[$cnt]) ? $row_data[$cnt] : null;

						if (is_null($value))
						{
						}

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

					$errors[] = static::save_object($arr, $format);
				}
			}

			return true;
		}
		else
		{
			return false;
		}



		$upload_path = APPPATH.'/tmp/import/';
	}



	/**
	 * save_object()
	 * Override 用
	 * 複雑なリレーションに対応する場合は Override する
	 */
	protected function save_object($arr, $format)
	{
		$model = $format->model;
		$obj = $model::forge($arr);

		try
		{
			$obj->save();
		}
		catch (\Fuel\Core\Database_Exception $e)
		{
			\Log::error($e);
		}


		return false;
	}



	/**
	 * 駄目だった関数
	 * Microsoft Excel から csv 化した時に [ " ] で囲まれないカラムがある
	 * かつ、[ , ] と [ \n ] がカラム内になったときのみ [ " ] で囲むので
	 * Format の正規表現 (Format->_from_csv) では判定出来ない
	 */
	/*
	protected function _import($format)
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

			$errors = array();

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

					$errors[] = static::save_object($arr, $format);
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	*/


}
