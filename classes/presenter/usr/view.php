<?php
class Presenter_Usr_View extends \Presenter_Base
{
	/**
	 * plain()
	 * @return obj instanceof \Form
	 */
	public static function plain($obj = null)
	{
		$form = \Presenter_Usr_Edit::form($obj);

		$form->delete('password');
		$form->delete('old_password');
		$form->delete('confirm_password');
		
		return $form->build_plain();
	}
}
