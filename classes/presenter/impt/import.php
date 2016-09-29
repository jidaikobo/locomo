<?php
namespace Locomo;
class Presenter_Impt_Import extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		// populate なし
		$form = \Fieldset::forge('impt');

		// field の成形
		$config = \Config::load('form', true);

		$form->add('format', 'フォーマット')->set_template(str_replace('{field}', $obj->name, $config['field_template']));

		// uploads
		$form->add(
			'upload[]',
			'アップロード',
			array('type' => 'file')
		);

		// uploads
		$form->add(
			'ignore_one_line',
			'1行目を無視',
			array(
				'type' => 'checkbox',
				'value' => 1
			));

		// submit
		$form->add('submit', '', array('type' => 'submit', 'value' => 'インポート', 'class' => 'button primary'))->set_template('<div class="submit_button">{field}</div>');

		return $form;


	}

}


