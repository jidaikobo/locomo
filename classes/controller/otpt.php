<?php
namespace Locomo;
class Controller_Otpt extends \Controller
{
	public $model_name = ''; //'\Locomo\Model_Pdf_format';

	use \Controller_Otpt_Pdf;
	use \Controller_Otpt_Excel;

	protected static $_pdf = null;
	protected static $_excel = null;

	public function before()
	{
		parent::before();
		\Fuel\Core\Fuel::$profiling = false;
	}

	public function after($response)
	{
		exit(); // 余分なレスポンスは返さない
	}

	public function pdf_init()
	{
		\Package::load('pdf');
		static::$_pdf = \Pdf::forge();
		static::$_pdf->setting();
		if (!defined('PDF_TEMP_PATH')) define('PDF_TEMP_PATH', APPPATH.'/locomo/assets/pdftemplate/');

		return static::$_pdf;
	}

	public function excel_init()
	{
		\Package::load('excel');
		static::$_excel = \Excel\Excel::forge();

		return static::$_excel;
	}

	public function __get($name)
	{
		if ($name == 'pdf')
		{
			if (static::$_pdf)
			{
				return static::$_pdf;
			}
			else
			{
				return $this->pdf_init();
			}
		}

		if ($name == 'excel')
		{
			if (static::$_excel)
			{
				return static::$_excel;
			}
			else
			{
				return $this->excel_init();
			}
		}
	}

	/*
	 * action_index_admin 他の Controller で parent::index_admin を読んでいるものから来る想定
	 * POST されたフォーマットに応じてメソッドを走らせる
	 * $this->model_name 設定必須
	 * \Input::post('format') or \Input::post('format1')
	 * \Input::post('ids')
	 * \Input::post('multiple_start') or \Input::post('multiple_start1') タックシールがある場合などは、この採番のセルから印刷する
	 */
	public function action_output()
	{
		if (! $this->model_name)
		{
			throw new \Exception('Undefined $this->model_name.');
		}

		$model = $this->model_name;

		if (\Input::post('submit1')) {
			$format_id = \Input::post('format1', \Input::post('format', false));
		} else {
			$format_id = \Input::post('format', \Input::post('format1', false));
		}

		$format = $model::find($format_id);

		if (! $format)
		{
			\Session::set_flash('error', 'フォーマットが選択されていません');
			$referrer = \Input::referrer(\Uri::create('/'));
			if (\Input::get())
			{
				$char = strpos($referrer, '?') === false ? '?' : '&';
				if (is_string(\Input::get())) {
					$referrer .= $char.str_replace('%3A', ':', \Input::get());
				} else {
					$referrer .= $char.str_replace('%3A', ':', http_build_query(\Input::get()));
				}
			}
			\Response::redirect($referrer, 'location', 307); // 307 post も維持してリダイレクト
		}


		$format_model = $format->model;
		$format_model::set_public_options();
		$format_model::set_search_options();

		// print_all で全件印刷
		if (\Input::post('print_all') || \Input::post('print_all1'))
		{
			$format_model::set_paginated_options();
			if ( isset($format_model::$_options['limit']) ) unset($format_model::$_options['limit']);
			if ( isset($format_model::$_options['offset']) ) unset($format_model::$_options['offset']);
			if ( isset($format_model::$_options['rows_limit']) ) unset($format_model::$_options['rows_limit']);
			if ( isset($format_model::$_options['rows_offset']) ) unset($format_model::$_options['rows_offset']);
		}
		else
		{
			$ids = \Input::post('ids');
			if (! $ids)
			{
				\Session::set_flash('error', '印刷項目が選択されていません');
				$referrer = \Input::referrer(\Uri::create('/'));
				if (\Input::get())
				{
					$char = strpos($referrer, '?') === false ? '?' : '&';
					if (is_string(\Input::get())) {
						$referrer .= $char.str_replace('%3A', ':', \Input::get());
					} else {
						$referrer .= $char.str_replace('%3A', ':', http_build_query(\Input::get()));
					}
				}
				\Response::redirect($referrer, 'location', 307); // 307 post も維持してリダイレクト
			}
			$format_model::set_paginated_options();
			$format_model::$_options['where'][] = array('id', 'IN', $ids);
		}

		$objects = $format_model::find('all', $format_model::$_options);

		$objects = static::convert_objects($objects, $format);

		// 繰り返しに対応
		if (\Input::post('print_repeat') || \Input::post('print_repeat1'))
		{
			$repeat = max(intval(\Input::post('print_repeat', \Input::post('print_repeat1', 0))) , 1);
			if ($repeat > 1)
			{
				$repeat_objects = array();
				foreach ($objects as $object)
				{
					for ($i=0; $i<$repeat; $i++)
					{
						$repeat_objects[] = $object;
					}
				}
				$objects = $repeat_objects;
			}
		}

		if (! $objects) // ほぼあり得ない($ids の時点で飛ばしているので)
		{
			\Session::set_flash('error', '項目が見つかりませんでした');

			$referrer = \Input::referrer(\Uri::create('/'));
			if (\Input::get())
			{
				$char = strpos($referrer, '?') === false ? '?' : '&';
				if (is_string(\Input::get()))
				{
					$referrer .= $char.str_replace('%3A', ':', \Input::get());
				}
				else
				{
					$referrer .= $char.str_replace('%3A', ':', http_build_query(\Input::get()));
				}
			}
			\Response::redirect($referrer, 'location', 307); // 307 post も維持してリダイレクト
		}

		return $this->output($objects, $format);

		// ここまでに return しなかったらページはない
		throw new \HttpNotFoundException;
	}

	/*
	 * Override 用
	 * フィールドの出力を変えたい時などに使う
	 */
	protected static function convert_objects($objects, $format)
	{
		return $objects;
	}


	/*
	 * Override 用
	 * テーブルのフィールドの出力を変えたい時などに使う
	 */
	protected static function convert_table_objects($table_objects, $table_format)
	{
		return $objects;
	}



	// convert_formats trait pdf に

	/*
	 * デフォルトの action 用のカプセル化
	 * id が渡されたら model でfind して、配列化して返す
	 */
	protected static function find_objects($id, $model)
	{
		if (is_array($id))
		{
			$objects = $id;
		}
		else
		{
			if (is_object($id))
			{
				$objects = array($id);
			}
			else
			{
				$model::set_authorized_options();
				$model::$_options['from_cache'] = false;
				$object = $model::find($id, $model::$_options);
				if (! $object)
				{
					throw new \HttpNotFoundException;
				}
				$objects = array($object);
			}
		}
		return $objects;
	}


	/*
	 * action_single()
	 * target _blank 等リンクで使用する
	 */
	public function action_single($format_id = null, $object_id = null)
	{
		if (! $this->model_name)
		{
			throw new \Exception('Undefined $this->model_name.');
		}

		$model = $this->model_name;
		$format = $model::find($format_id);

		$format_model = $format->model;
		$format_model::set_authorized_options();

		if (
			! $format ||
			! $object = $format_model::find($object_id, $format_model::$_options))
		{
			throw new \HttpNotFoundException;
		}

		$objects = array($object);
		$objects = static::convert_objects($objects, $format);

		$this->output($objects, $format);
	}


	/*
	 * output()
	 * action_output, action_single をここで処理
	 */
	protected function output($objects, $format)
	{

		// TODO
		// そのうち、こっちに addPage を積んで、
		// foreach の$object ごとにデフォルトのフォーマットと
		// element 両方呼ぶ処理に切り替える
		// $pdf->output もこちらに

		switch ($format->type)
		{
			case 'pdf':
				if ($format->is_multiple)
				{
					if (\Input::post('submit1')) {
						$start_cell = \Input::post('start_cell1', \Input::post('start_cell', false));
					} else {
						$start_cell = \Input::post('start_cell', \Input::post('start_cell1', false));
					}
					return $this->pdf_multiple($objects, $format, $start_cell);
				}
				else
				{
					return $this->pdf_single($objects, $format);
				}
				break;
			case 'excel':
				return $this->excel($objects, $format);
				break;
			case 'csv':
				return $this->csv($objects, $format);
				break;
			default:
				if (method_exists($this, $format->type))
				{
					$action_name = $format->type;
					$this->$action_name($objects, $format);
				}
				else if (method_exists($this, 'action_'.$format->type))
				{
					$action_name = 'action_'.$format->type;
					$this->$action_name($objects, $format);
				}
				break;
		}
	}
}
