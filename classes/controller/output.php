<?php
namespace Locomo;
class Controller_Output extends \Controller
{
	public $model_name = ''; //'\Locomo\Model_Pdf_format';

	use \Controller_Output_Pdf;
	use \Controller_Output_Excel;

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
		$format_id = \Input::post('format', \Input::post('format1', false));

		$format = $model::find($format_id);

		$ids = \Input::post('ids');
		if (! $format || !$ids)
		{
			if (! $format)  \Session::set_flash('error', 'フォーマットが選択されていません');
			if (! $ids) \Session::set_flash('error', '印刷項目が選択されていません');

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

		$format_model = $format->model;
		$format_model::set_public_options();
		$format_model::set_search_options();
		$format_model::set_paginated_options();
		$format_model::$_options['where'][] = array('id', 'IN', $ids);
		$objects = $format_model::find('all', $format_model::$_options);

		$objects = static::convert_objects($objects);

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

		switch ($format->type)
		{
			case 'pdf':
				if ($format->is_multiple)
				{
					return $this->pdf_multiple($format, $objects);
				}
				else
				{
					return $this->pdf($format, $objects);
				}
				break;
			case 'excel':
				return $this->excel($format, $objects);
				break;
			case 'csv':
				return $this->csv($format, $objects);
				break;
			default:
				if (method_exists($this, $format->type))
				{
					$action_name = $format->type;
					$this->$action_name($format, $objects);
				}
				else if (method_exists($this, 'action_'.$format->type))
				{
					$action_name = 'action_'.$format->type;
					$this->$action_name($format, $objects);
				}
				break;
		}

		// ここまでに return しなかったらページはない
		throw new \HttpNotFoundException;
	}

	/*
	 * Override 用
	 * フィールドの出力を変えたい時などに使う
	 */
	protected static function convert_objects($objects)
	{
		return $objects;
	}

	/*
	 * フォーマットの変更用
	 * Override する際は parent で呼ぶ
	 */
	protected static function convert_formats($element)
	{
		$format_arr = array();

		$defaults = array(
			'ln' => 2,
		);

		foreach ($element as $elm)
		{
			$arr = $elm->to_array();

			$arr = array_merge($defaults, $arr);
			// テキストの処理
			$fields = explode('}', str_replace('{', '}', $elm->txt));
			$arr['fields'] = $fields;

			if ($elm->h_adjustable) {
				$arr['fitcell']  = false;
				$arr['maxh'] = 0;
				$arr['h'] = 0;
			} else {
				$arr['fitcell'] = true;
				$arr['maxh'] = $elm->h;
			}

			if ($elm->ln_y) {
				unset($arr['y']);
			} else {
			}

			$border_str = '';
			if ($elm->border_left) $border_str .= 'L';
			if ($elm->border_top) $border_str .= 'T';
			if ($elm->border_right) $border_str .= 'R';
			if ($elm->border_bottom) $border_str .= 'B';
			$arr['border'] = $border_str;

			$format_arr[] = $arr;
		}

		return $format_arr;
	}


}

