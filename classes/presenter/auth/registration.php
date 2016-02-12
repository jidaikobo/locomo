<?php
namespace Locomo;
class Presenter_Auth_Registration extends Presenter_Usr_Edit
{
	/*
	 * form
	 */
	public static function form($obj = NULL)
	{
		$form = parent::form($obj);
		$form->field('username')
				->set_type('text');
		$form->delete('display_username');
		$form->delete('old_password');
		$form->delete('usergroup');
		$form->field('submit')->set_value('登録する');
		return $form;
	}
}
