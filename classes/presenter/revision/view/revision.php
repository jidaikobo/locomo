<?php
class Presenter_Revision_View_Revision extends \Presenter_Base
{
	/**
	 * plain()
	 * @return obj instanceof \Form
	 */
	public static function plain($obj = null)
	{
		$form = parent::form($obj);

		$form->delete('password');
		
		return $form->build_plain();
	}

}
