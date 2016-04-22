<?php
/*
 * Base のコントローラー 直接は呼ばない
 */
namespace Locomo;
class Controller_Frmt extends \Controller_Base
{

	public $model_name = '\Locomo\Model_Frmt';

	public $output_url;

	/**
	 * pdf_edit()
	 */
	public function pdf_edit($id = null)
	{
		\Asset::css('frmt/pdf/edit.css', array(), 'css');
		\Asset::js('frmt/pdf/edit.js', array(), 'js');
		$this->_content_template = $this->_content_template ?: 'frmt/pdf/edit';
		$obj = parent::edit($id, false);

		if (!$obj)
		{
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

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

		$item = $model::find($id, $model::$_options);

		if (!$item)
		{
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

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
				// var_dump(\Input::post()); die();
			}
		}
		$content = \Presenter::forge($this->_content_template ?: 'frmt/pdf/edit/element');
	//	$content->view();
////		var_dump( $content->setElements(array(1  => 'hoge'))); die();
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
		$this->template->set_global('title', $item->name . ' 要素編集');
	}

	/*
	 * excel_edit
	 */
	protected function excel_edit($id = null)
	{
		$model = $this->model_name;

		$this->_content_template = $this->_content_template ?: 'frmt/excel/edit';

		$obj = static::edit($id, false);

		if (!$obj)
		{
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

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

		$item = $model::find($id, $model::$_options);

		if (!$item)
		{
			$page = \Request::forge('sys/403')->execute();
			$this->template->set_safe('content', $page);
			return new \Response($page, 403);
		}

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
				// var_dump(\Input::post()); die();
			}
		}

		$content = \Presenter::forge($this->_content_template ?: 'frmt/excel/edit/element');
		$content->get_view()->set('item', $item);

		$content->get_view()->set('model_properties', $model::$_format_excel_fields);
		$this->template->set_safe('content', $content);
		$this->template->set_global('title', $item->name . ' 要素編集');
	}

	/**
	 * template
	 */
	protected function template($template)
	{
	}


	/*
	 * action_copy()
	 */
	public function copy($id = null)
	{
		$model = $this->model_name;
		$parent = $model::find($id);

		if (! $parent)
		{
			\Session::set_flash('success', '不正なリクエストです。');
			\Response::redirect(\Input::referrer());
		}

		$copy = $model::forge($parent->to_array());
		unset($copy->id);
		$copy->name = $parent->name.'の複製';
		$copy->is_draft = true;
		$copy->deleted_at = null;
		if ($copy->save())
		{
			$element_model = $model::relations('element')->model_to;
			foreach ($parent->element as $element)
			{
				$elm_cpy = $element_model::forge($element->to_array());
				$elm_cpy->format_id = $copy->id;
				unset($elm_cpy->id);
				$copy->element[] = $elm_cpy;
			}
			if ($copy->save())
			{
				\Session::set_flash('success', 'ID: '.$copy->id.'に、フォーマットID: '.$parent->id.' '.$parent->name.'の複製を作成しました。(使用する際は"下書き"から"使用中"に変更して下さい)');
				\Response::redirect(\Input::referrer());
			}
		}
		\Session::set_flash('error', '作成に失敗しました。もう一度やり直して下さい。');
		\Response::redirect(\Input::referrer());
	}


	/* ==========
	 * actions
	========== */

		/*
	 * action_index_admin
	 */
	public function action_index_admin()
	{
		if (!$this->_content_template) $this->_content_template = 'frmt/index_admin';

		parent::index_admin();

		if ($this->output_url) $this->template->content->output_url = \Uri::create($this->output_url);
	}


	/*
	 * action_index_deleted
	 */
	public function action_index_deleted ()
	{
		if (!$this->_content_template) $this->_content_template = 'frmt/index_admin';
		parent::index_deleted();
		$this->template->content->output_url = false;
	}


	/**
	 * action_view()
	 */
	public function action_view($id = null)
	{
		static::view($id);
	}

	/*
	 * action_delete()
	 * @param Integer
	 */
	public function action_delete($id = null)
	{
		parent::delete($id);
	}

	/*
	 * action_undelete()
	 * @param Integer
	 */
	public function action_undelete($id = null)
	{
		parent::undelete($id);
	}

	/**
	 * action_pdf_create()
	 */
	public function action_pdf_create($id = null)
	{
		static::pdf_edit($id);
	}

	/**
	 * action_pdf_edit()
	 */
	public function action_pdf_edit($id = null)
	{
		static::pdf_edit($id);
	}

	/**
	 * action_pdf_edit()
	 */
	public function action_pdf_edit_element($id = null)
	{
		static::pdf_edit_element($id);
	}

	/**
	 * action_excel_create()
	 */
	public function action_excel_create($id = null)
	{
		$obj = static::excel_edit($id);
	}

	/**
	 * action_excel_edit()
	 */
	public function action_excel_edit($id = null)
	{
		$obj = static::excel_edit($id);
	}

	/**
	 * action_excel_edit_element()
	 */
	public function action_excel_edit_element($id = null)
	{
		static::edit_excel_element($id);
	}

	/*
	 * action_copy()
	 */
	public function action_copy($id = null)
	{
		static::copy($id);
	}
}


