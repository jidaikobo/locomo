<?php
namespace Locomo;
class Presenter_Frmt_Table_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

		$options = array();

		foreach ($obj::$_format_table_fields as $key => $value)
		{
			$options[$key] = $value['name'];
		}

		$form->field('relation')->set_options($options);

		return $form;
	}



	protected static function _setElements($elements)
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


}
