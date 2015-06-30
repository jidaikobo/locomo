<?php
class Presenter_Flr_File_Upload extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

		$form->field('name')->set_type('hidden');
		$form->add_after('display_name', 'ディレクトリ名', array('type' => 'text', 'disabled' => 'disabled'),array(), 'name')->set_value(@$obj->name);
		$form->add_after(
			'upload',
			'アップロード',
			array('type' => 'file'),
			array(),
			'display_name'
		)
		->add_rule(array('valid_string' => array('alpha','numeric','dot','dashes')));

		$form->field('submit')->set_value('アップロード');

		return $form;
	}
}
