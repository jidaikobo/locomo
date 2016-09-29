<?php
/*
 * Base のコントローラー 直接は呼ばない
 * インポートのメソッドは trait に
 */
namespace Locomo;
class Controller_Impt extends \Controller_Base
{
	use \Controller_Traits_Impt;

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
			$import_result = static::import($format);

			if ($import_result)
			{
				\Session::set_flash('success' , 'インポートが完了いたしました');
			}
			else
			{
				\Session::set_flash('error' , 'ファイルのアップロードに失敗いたしました');
			}
		}

		$content = \Presenter::forge($this->_content_template ?: static::$dir.'import');
		$form = $content::form($format);

		$content->get_view()->set_global('item', $format, false);
		$content->get_view()->set_global('form', $form, false);
		$this->template->set_safe('content', $content);
		$this->template->set_global('title', self::$nicename);
	}
}
