<?php
namespace XXX;
class Presenter_XXX_Index_Admin extends \Presenter_Base
{
	/*
	 * search_form
	 */
	public static function search_form($title)
	{
		// parent
		$form = parent::search_form($title);

/*
		// free word search - sample
		$form->add_after(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all')),
			array(),
			'opener'
		);
*/
		return $form;
	}
}
