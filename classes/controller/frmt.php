<?php
/*
 * Base のコントローラー 直接は呼ばない
 */
namespace Locomo;
class Controller_Frmt extends \Locomo\Controller_Base
{

	public $model_name = '\Locomo\Model_Frmt';


	/**
	 * pdf_edit()
	 */
	public function pdf_edit($id = null)
	{
		\Asset::css('frmt/pdf/edit.css', array(), 'css');
		\Asset::js('frmt/pdf/edit.js', array(), 'js');
		$this->_content_template = $this->_content_template ?: 'frmt/pdf/edit';
		$obj = parent::edit($id, false);
		if (\Input::post() && $obj) \Response::redirect(static::$base_url.'pdf_edit/'.$obj->id);
	}

	/**
	 * pdf_edit_element()
	 */
	public function pdf_edit_element($id)
	{
		$model = $this->model_name;

		\Asset::css('frmt/pdf/edit/element.css', array(), 'css');
		\Asset::js('frmt/pdf/edit/element.js', array(), 'js');

		$item = $model::find($id);

		if (\Input::post())
		{
			// element 既存列
			foreach ($item->element as $k => $v) {
				if (! \input::post('element.'.$k)) {
					unset($item->element[$k]);
				} else {
					if ($item->element[$k]) {
						$item->element[$k]->set(\input::post('element.'.$k));
					}
				}
			}
			// element 新規列
			if (\Input::post('element_new')) {
				foreach (\Input::post('element_new') as $k => $v) {
					$new_element = \Locomo\Model_Frmt_Element::forge($v);
					$new_element->form_key = $k;
					$item->element[] = $new_element;
				}
			}

			if ($item->save())
			{
				\Session::set_flash('success', '保存しました');
				\Response::redirect(\Uri::current());
			}
			else 
			{
				var_dump(\Input::post()); die();
			}
		}

		$content = \Presenter::forge($this->_content_template ?: 'frmt/pdf/edit/element');
		$content->get_view()->set('item', $item);
		if ($item->is_multiple)
		{
			$content->get_view()->set('print_width', $item->cell_w);
			$content->get_view()->set('print_height', $item->cell_h);
		}
		else
		{
			$content->get_view()->set('print_width', $item->w);
			$content->get_view()->set('print_height', $item->h);
		}

		$content->get_view()->set('model_properties', $model::$_format_pdf_fields);
		$this->template->set_safe('content', $content);
		$this->template->set_global('title', 'PDFフォーマット編集');
	}

	/*
	 * excel_edit
	 */
	protected function excel_edit($id = null)
	{
		$model = $this->model_name;

		$this->_content_template = $this->_content_template ?: 'frmt/excel/edit';

		$obj = static::edit($id, false);

		if (\Input::post() && $obj)
		{
			if (\Input::post('submit_to_element')) {
				\Response::redirect(static::$base_url.'excel_edit_element/'.$obj->id);
			} else {
				\Response::redirect(static::$base_url.'excel_edit/'.$obj->id);
			}
		}
	}

	/*
	 * edit_excel_element
	 */
	public function edit_excel_element($id)
	{
		$model = $this->model_name;

		\Asset::css('frmt/excel/edit/element.css', array(), 'css');
		\Asset::js('frmt/excel/edit/element.js', array(), 'js');

		$item = $model::find($id);

		if (\Input::post())
		{
			// element 既存列
			foreach ($item->element as $k => $v) {
				if (! \input::post('element.'.$k)) {
					unset($item->element[$k]);
				} else {
					if ($item->element[$k]) {
						$item->element[$k]->set(\input::post('element.'.$k));
					}
				}
			}
			// element 新規列
			if (\Input::post('element_new')) {
				foreach (\Input::post('element_new') as $k => $v) {
					$new_element = \Locomo\Model_Frmt_Element::forge($v);
					$new_element->form_key = $k;
					$item->element[] = $new_element;
				}
			}

			if ($item->save())
			{
				\Session::set_flash('success', '保存しました');
				\Response::redirect(\Uri::current());
			}
			else 
			{
				var_dump(\Input::post()); die();
			}
		}

		$content = \Presenter::forge($this->_content_template ?: 'frmt/excel/edit/element');
		$content->get_view()->set('item', $item);

		$content->get_view()->set('model_properties', $model::$_format_pdf_fields);
		$this->template->set_safe('content', $content);
		$this->template->set_global('title', 'PDFフォーマット編集');
	}

	/*
	 * copy
	 */
	public function copy($item)
	{
		return $copy;
	}
}


