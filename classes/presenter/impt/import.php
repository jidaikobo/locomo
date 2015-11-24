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

		// submit
		$form->add('submit', '', array('type' => 'submit', 'value' => '保存', 'class' => 'button primary'))->set_template('<div class="submit_button">{field}</div>');

		// field の成形
		$config = \Config::load('form', true);

		// uploads
		$form->add(
			'upload[]',
			'アップロード',
			array('type' => 'file', 'multiple' => 'multiple', 'description' => '複数ファイル可')
		);

		// uploads
		$form->add(
			'ignore_one_line',
			'1行目を無視',
			array(
				'type' => 'checkbox',
				'value' => 1
			));

		return $form;


	}

}


