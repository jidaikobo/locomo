<?php
namespace Locomo;
class Presenter_Frmt_Excel_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = \Fieldset::forge('format_excel');

		$form->add('name', '名前', array('template' => 'opener'), array('required'))->set_value(\Input::post('name', $obj->name));
		$form->add('seq', '表示順', array('size' => 5, 'class' => 'ar'), array())->set_value(\Input::post('seq', $obj->seq));
		$form->add('is_draft', '下書き', array('type' => 'radio', 'options' => array(0=>'使用', 1=>'下書き'), 'template' => 'closer'), array())->set_value(\Input::post('is_draft', $obj->is_draft));
		$form->add('type', '', array('type' => 'hidden'), array())->set_value('excel');

		// submit
		if (! $obj->is_new()) {
			$form->add('submit', '', array('type' => 'submit', 'value' => '保存', 'class' => 'button primary'))->set_template('<div class="submit_button">{field}');
			$form->add('submit_to_element', '', array('type' => 'submit', 'value' => '保存して要素編集へ', 'class' => 'button primary'))->set_template('{field}</div>');
		} else {
			$form->add('submit_to_element', '', array('type' => 'submit', 'value' => '保存して要素編集へ', 'class' => 'button primary'))->set_template('<div class="submit_button">{field}</div>');
		}

		return $form;
	}
}
