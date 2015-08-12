<?php
namespace Locomo;
class Presenter_Frmt_Excel_Edit extends \Presenter_Base
{
	public function view()
	{
		$this->setElements = function ($elements)
		{
			return static::setElements($elements);
		};
		$this->templateElement = function ()
		{
			return static::setElements(\Locomo\Model_Frmt_Element::forge());
		};

		$this->modelPropertiesForm = function ($model_properties = array())
		{
			if ($model_properties) static::setModelProperties($model_properties);
			return static::modelPropertiesForm($model_properties);
		};
	}


	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = \Fieldset::forge('format_excel');

		$form->add('name', '名前', array('template' => 'opener'), array('required'))->set_value(\Input::post('name', $obj->name));
		$form->add('seq', '表示順', array('template' => 'closer', 'size' => 5, 'class' => 'ar'), array())->set_value(\Input::post('seq', $obj->seq));
		$form->add('type', '表示順', array('type' => 'hidden'), array())->set_value('excel');

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
