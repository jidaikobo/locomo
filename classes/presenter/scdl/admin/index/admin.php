<?php
class Presenter_Scdl_Admin_Index_Admin extends \Presenter_Base
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

		// ユーザグループ
		$options = array('' => '選択してください');
		$options+= \Model_Usrgrp::find_options('name', array('where' => array(array('is_available', true)), 'order_by' => array('name')));
		$form->add_after(
				'usergroup',
				'ユーザグループ',
				array('type' => 'select', 'options' => $options),
				array(),
				'all'
			)
			->set_value(\Input::get('usergroup'));

		// 登録日 - 開始
		$form->add_after(
				'from',
				'登録日',
				array(
					'type'        => 'text',
					'value'       => \Input::get('from'),
					'id'          => 'registration_date_start',
					'class'       => 'date',
					'placeholder' => date('Y-n-j', time() - 86400 * 365),
					'title'       => '登録日 開始 ハイフン区切りで入力してください',
				),
				array(),
				'usergroup'
			)
			->set_template('
				<div class="input_group">
				<h2>登録日</h2>
				{field}&nbsp;から
			');

		// 登録日 - ここまで
		$form->add_after(
				'to',
				'登録日',
				array(
					'type'        => 'text',
					'value'       => \Input::get('to'),
					'id'          => 'registration_date_end',
					'class'       => 'date',
					'placeholder' => date('Y-n-j'),
					'title'       => '登録日 ここまで ハイフン区切りで入力してください',
				),
				array(),
				'from'
			)
			->set_template('
				{field}</div><!--/.input_group-->
			');

		return $form;
	}
}
