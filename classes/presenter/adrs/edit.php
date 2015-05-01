<?php
class Presenter_Adrs_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

		// group_id
		$options = array('' => '選択してください');
		$options+= \Model_Adrsgrp::find_options(
			'name',
			array(
				'where' => array(array('is_available', 1)),
				'order_by' => array('seq' => 'ASC')
			)
		);
		$form->field('group_id')
				->set_type('select')
				->set_options($options)
				->set_value($obj->group_id);

		// zip
		$form->field('zip3')
			->set_template("
				\t\t<div class=\"input_group\">\n
				\t\t\t<h2>{required}{label}</h2>\n
				\t\t\t<div class=\"field\">\n
				\t\t\t\t<em class=\"exp\">{error_msg}{description}</em>\n
				\t\t\t\t{field}{error_alert_link}\n
				\t\t\t\t-\n
			");
		$form->field('zip4')
			->set_template("
					\t\t\t\t<em class=\"exp\">{error_msg}{description}</em>\n
					\t\t\t\t{field}{error_alert_link}\n
				\t\t\t</div>\n
				\t\t</div>\n
				");

		return $form;
	}
}
