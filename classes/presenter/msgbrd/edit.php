<?php
namespace Locomo;
class Presenter_Msgbrd_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

		// usergroup_id
		$options = array('' => '選択してください', '-10' => 'ログインユーザすべて');
		if ( ! \Config::get('no_home'))
		{
			\Arr::insert_assoc(
				$options,
				array('0' => '一般公開'),
				1
			);
		}
		$options+= \Model_Usrgrp_Custom::find_options();
		$form->field('usergroup_id')
			->set_options($options)
			->set_value($obj->usergroup_id);

		// categories
		$options = array('' => '選択してください');
		$options+= \Model_Msgbrd_Categories::find_options('name', array('where' => array(array('is_available', 1)), 'order_by' => array('seq' => 'ASC', 'name' => 'ASC')));
		$form->field('category_id')
			->set_options($options)
			->set_value($obj->category_id);

		return $form;
	}
}
