<?php
namespace Locomo;
class Presenter_Frmt_Excel_Edit_Element extends \Presenter_Base
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

		return $form;
	}


	public static function setElements($elements)
	{
		$result_str = '';

		if (! is_array($elements)) $elements = array($elements);

		\Config::set('form.field_template', \Config::get('form')['tabular_row_field_template']);
		foreach ($elements as $element)
		{
			$form_id = $element->id ? 'element_' . $element->id : 'element_new_$';
			if (isset($element->form_key)) $form_id = str_replace('$', $element->form_key, $form_id);
			$form = \Fieldset::forge($form_id);

			// seq
			$name = $element->id ? 'element[' . $element->id .'][seq]' : 'element_new[$][seq]';
			if (isset($element->form_key)) $name = str_replace('$', $element->form_key, $name);
			$form->add($name, '', array(
				'type' => 'hidden',
				'class' => 'seq ar w5em',
				'readonly' => 'readonly',
				'value' => \Input::post($form->get_name().'.seq', $element->seq),
			));

			// name
			$name = $element->id ? 'element[' . $element->id .'][name]' : 'element_new[$][name]';
			if (isset($element->form_key)) $name = str_replace('$', $element->form_key, $name);
			$form->add($name, '', array(
				'type' => 'hidden',
				'class' => 'name',
				'value' => \Input::post($form->get_name().'.name', $element->name),
			));

			// txt
			$name = $element->id ? 'element[' . $element->id .'][txt]' : 'element_new[$][txt]';
			if (isset($element->form_key)) $name = str_replace('$', $element->form_key, $name);
			$form->add($name, '', array(
				'type' => 'hidden',
				'class' => 'txt',
				'value' => \Input::post($form->get_name().'.txt', $element->txt),
			));

			$form = '
				<div class="display_seq">'.$element->seq.'</div>
				<div class="display_name">'.$element->name.'</div>
				<div class="text_wrapper" >
					<div class="text">'.$element->txt.'</div>
				</div>' . $form;

			$result_str .= html_tag('li', array(
				'id' => $form_id,
				'class' => 'element'
			), $form);
		}
		return $result_str;

	}


	protected static function modelPropertiesForm()
	{

		$html = '<ul id="model_properties">';
		// モデルのプロパティを追加
		foreach (static::$_model_properties as $prop_name => $label)
		{
			$html .= '<li>';
			$html .= $label;
			$html .= '<button name="field_'.$prop_name.'" id="form_field_'.$prop_name.'" data-field="'.$prop_name.'" class="field_'.$prop_name.' btn small add_txt" value="'.$label.'" type="button">データに追加</button>';
			$html .= '<button name="field_'.$prop_name.'" id="form_field_'.$prop_name.'" data-field="'.$prop_name.'" class="field_'.$prop_name.' btn small add_row" value="'.$label.'" type="button">新規列として追加</button>';
			$html .= '</li>';
		}
		$html .= '</ul>';

		return $html;
	}

	protected static $_model_properties = array();
	public static function setModelProperties($model_properties, $merge = true)
	{
		if ($merge)
		{
			static::$_model_properties = array_merge(static::$_model_properties, $model_properties);
		}
		else
		{
			static::$_model_properties = $model_properties;
		}
	}

}


