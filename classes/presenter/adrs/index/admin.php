<?php
namespace Locomo;
class Presenter_Adrs_Index_Admin extends \Presenter_Base
{
	/*
	 * search_form
	 */
	public static function search_form($title)
	{
		// Fieldset::forge
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge('msgbrd', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// グループ
		$options = array('' => '選択してください');
		$options+= \Model_Adrsgrp::find_options(
			'name',
			array(
				'where' => array(array('is_available', 1)),
				'order_by' => array('seq' => 'ASC')
			)
		);
		$form->add(
				'group',
				'グループ',
				array('type' => 'select', 'options' => $options)
			)
			->set_value(\Input::get('group'));

		// wrap
		$parent = parent::search_form($title);
		$parent->add_after($form, 'msgbrd', array(), array(), 'opener');

		return $parent;
	}
}
