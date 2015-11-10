<?php
namespace Locomo;
class Presenter_Usrgrp_Index_Admin extends \Presenter_Base
{
	/*
	 * search_form
	 */
	public static function search_form($title)
	{
		// parent
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge('usrgrps', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// wrap
		$parent = parent::search_form($title);
		$parent->add_after($form, 'user', array(), array(), 'opener');

		return $parent;
	}
}
