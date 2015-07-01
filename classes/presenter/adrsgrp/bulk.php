<?php
class Presenter_Adrsgrp_Bulk extends \Presenter_Base
{
	public static function bulk($name = 'usergroup', $obj = null)
	{
		$form = \Fieldset::forge($name, \Config::get('form'));

		//id
		$form->add(
			'id',
			'ID',
			array('type' => 'text', 'disabled' => 'disabled', 'size' => 2)
		)
		->set_value(@$obj->id);

		//name
		$form->add(
				'name',
				'グループ名',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->name)
			->add_rule('required')
			->add_rule('max_length', 50);
			//->add_rule('unique', "lcm_adrs_groups.name");

		//description
		$form->add(
				'description',
				'説明',
				array('type' => 'text', 'size' => 20)
			)
			->set_value(@$obj->description)
			->add_rule('max_length', 255);

		//order
		$form->add(
				'seq',
				'表示順',
				array('type' => 'text', 'size' => 5)
			)
			->set_value(@$obj->seq)
			->add_rule('valid_string', array('numeric'));

		//is_available
		$form->add(
				'is_available',
				'使用中',
				array('type' => 'select', 'options' => array('0' => '未使用', '1' => '使用中'), 'default' => 0)
			)
			->set_value(@$obj->is_available);

		return $form;


	}
}
