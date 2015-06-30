<?php
class Presenter_Flr_Dir_Rename extends \Presenter_Base
{
	/**
	 * form()
	 */
	public static function form($obj = NULL)
	{
		$form = parent::form($obj);

		//$tpl = \Config::get('form')['field_template'];
		$form->field('name')->set_description('現在の名前：'.$obj->name);
		$form->delete('explanation');
		$form->delete('is_sticky');

		// parent dir
		$current_dir = @$obj->path ?: '';
		$current_dir = $current_dir ? rtrim(dirname($current_dir), '/').DS : '';

		$form->add_after(
				'parent',
				'親ディレクトリ',
				array('type' => 'text', 'disabled' => 'disabled', 'style' => 'width:100%;'),
				array(),
				'name'
			)
			->set_value(urldecode($current_dir));

		return $form;
	}
}
