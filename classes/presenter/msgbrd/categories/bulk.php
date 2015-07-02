<?php
class Presenter_Msgbrd_Categories_Bulk extends \Presenter_Base
{
	/*
	 * search_form
	 */
	public static function search_form($title)
	{
		// parent
		$form = parent::search_form($title);

		// 検索
		$form->add_after(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all')),
			array(),
			'opener'
		);

		return $form;
	}
}
