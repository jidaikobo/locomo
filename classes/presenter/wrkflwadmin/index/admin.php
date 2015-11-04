<?php
namespace Locomo;
class Presenter_Wrkflwadmin_Index_Admin extends \Presenter_Base
{
	/*
	 * search_form
	 */
	public static function search_form($title)
	{
		// Fieldset::forge
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge('wkflwadm', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// wrap
		$parent = parent::search_form('ワークフロー一覧');
		$parent->add_after($form, 'msgbrd', array(), array(), 'opener');

		return $parent;
	}
}
