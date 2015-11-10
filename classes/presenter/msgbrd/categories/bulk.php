<?php
namespace Locomo;
class Presenter_Msgbrd_Categories_Bulk extends \Presenter_Base
{
	/*
	 * search_form
	 */
	public static function search_form($title)
	{
		// parent
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge('msgbrd', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// wrap
		$parent = parent::search_form($title);
		$parent->add_after($form, 'msgbrd', array(), array(), 'opener');

		return $parent;
	}
}
