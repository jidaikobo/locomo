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

		// user
		$options = array('' => '選択してください');
		$options+= \Model_Usr::find_options('display_name');
		$form->field('user_id')
			->set_options($options)
			->set_value($obj->user_id);


		// categories
		$options = array('' => '選択してください');
		$options+= \Model_Msgbrd_Categories::find_options('name', array('where' => array(array('is_available', 1)), 'order_by' => array('seq' => 'ASC', 'name' => 'ASC')));
		$form->field('category_id')
			->set_options($options)
			->set_value($obj->category_id);

		// 返信の場合に元の値を反映する
		if (
			\Request::main()->action === 'create' &&
			$parent = Model_Msgbrd::find(\Input::get('parent_id'))
		)
		{
			$form->field('parent_id')->set_value($parent->id);
			$form->field('name')->set_value(\Input::post('name', 'Re:'.$parent->name));


			$form->field('category_id')->set_value(\Input::post('category_id', $parent->category_id));
			$form->field('usergroup_id')->set_value(\Input::post('usergroup_id', $parent->usergroup_id));
			$form->field('user_id')->set_value(\Input::post('user_id', $parent->creator_id)); // 差出人
		}

		return $form;
	}
}
