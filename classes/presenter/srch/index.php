<?php
namespace Locomo;
class Presenter_Srch_Index extends \Presenter_Base
{
	/*
	 * search_form
	 */
	public static function search_form($title)
	{
		// parent
		$form = parent::search_form($title);
		$tpl = \Config::get('form');

		// free word search
		$form->add_after(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all')),
			array(),
			'opener'
		);

		$form->field('clear_results')->set_template('<div class="submit_button">');

		// AND検索
		$tpl_and_srch = str_replace('<h2', '<h2 id="and_srch"', $tpl['multi_field_template']);
		$form->add_after(
			'and_srch',
			'検索条件',
			array('type' => 'checkbox', 'options' => array(1 => 'すべての条件を満たすものを表示'), 'value' => \Input::get('and_srch'), 'template' => $tpl_and_srch),
			array(),
			'all'
		);

		return $form;
	}
}
