<?php
class Presenter_Flr_File_Edit extends \Presenter_Base
{
	/**
	 * form()
	 */
	public static function form($obj = NULL)
	{
		$form = parent::form($obj);

		$form->field('name')->set_type('hidden');
		$form->add_after('display_file_name', 'ファイル名', array('type' => 'text', 'disabled' => 'disabled'),array(), 'name')->set_value(@$obj->name)->set_description('ファイル名を変更したい場合はアップし直してください。');
		$form->field('is_sticky')->set_description('画像の場合はダッシュボードの「ギャラリー」に表示されます。');

		return $form;
	}
}
