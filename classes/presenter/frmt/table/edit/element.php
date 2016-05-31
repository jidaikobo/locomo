<?php
namespace Locomo;
class Presenter_Frmt_Table_Edit_Element extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = \Fieldset::forge('format_excel');

		if (!$obj->type) $obj->type == 'excel';

		$form->add('is_draft', '下書き', array('type' => 'radio', 'options' => array(0=>'使用', 1=>'下書き')), array())
			->set_value(\Input::post('is_draft', $obj->is_draft));

		$form->add('name', '名前', array('template' => 'opener'), array('required'))
			->set_value(\Input::post('name', $obj->name));

		$form->add('seq', '表示順', array('size' => 5, 'class' => 'ar', 'template' => 'closer'), array())
			->set_value(\Input::post('seq', $obj->seq));

		$form->add('type', 'フォーマット', array(
			'type' => 'select',
			'options' => array(
				'excel' => 'xlsx',
				'csv'   => 'csv',
			)
		), array())->set_value(\Input::post('type', $obj->type));

		// submit
		if (! $obj->is_new()) {
			$form->add('submit', '', array('type' => 'submit', 'value' => '保存', 'class' => 'button primary'))->set_template('<div class="submit_button">{field}');
			$form->add('submit_to_element', '', array('type' => 'submit', 'value' => '保存して要素編集へ', 'class' => 'button primary'))->set_template('{field}</div>');
		} else {
			$form->add('submit_to_element', '', array('type' => 'submit', 'value' => '保存して要素編集へ', 'class' => 'button primary'))->set_template('<div class="submit_button">{field}</div>');
		}

		return $form;
	}

	public static function setElements($elements)
	{
		return static::_setElements($elements);
	}
	public static function templateElement()
	{
		return static::_setElements(\Locomo\Model_Frmt_Table_Element::forge());
	}

	protected static function _setElements($elements)
	{
		$result_str = '';
		$props = \Locomo\Model_Frmt_Table_Element::properties();
		if (! is_array($elements)) $elements = array($elements);

		$config = \Config::get('form'); //, \Config::get('form')['tabular_row_field_template']);
		foreach ($elements as $element)
		{
			$form_id = $element->id ? 'element_' . $element->id : 'element_new_$';
			if (isset($element->form_key)) $form_id = str_replace('$', $element->form_key, $form_id);

			$config['field_template'] = $config['tabular_row_field_template'];
			$form = \Fieldset::forge($form_id, $config);

			foreach ($props as $prop_name => $prop)
			{
				if (isset($prop['form']['type']) && $prop['form']['type'] == false) continue;
				$name = $element->id ? 'element[' . $element->id .'][' . $prop_name . ']' : 'element_new[$][' . $prop_name . ']';
				if (isset($element->form_key)) $name = str_replace('$', $element->form_key, $name);
				$form->add(
					$name,
					'',
					array(
						'type' => 'hidden',
						'class' => $prop_name,
						'value' => \Input::post($form->get_name().'.'.$prop_name, $element->{$prop_name}),
					)
				);
			}


			/*
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
			 */

			$form = '
				<div class="display_name">'.$element->name.'</div>
				<div class="table_header">
					<div class="display_seq">'.$element->seq.'</div>
					<div class="display_label">'.$element->label.'</div>
				</div>
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


	public static function setController($model_properties = array())
	{
		$form = \Fieldset::forge('controller');
		$form->add_model(\Locomo\Model_Frmt_Table_Element::forge());


		/*
		$field_no_label = array(
			'ln_y',
			'h_adjustable',
		);
		foreach ($field_no_label as $field)
		{
			$form->field($field)->set_template("
				\t\t<div class=\"input_group lcm_focus label_fb {type}\" tabindex=\"0\" title=\"{title_contents} {error_msg}\" data-jslcm-tooltip=\"{error_msg}\">\n
				\t\t\t<div class=\"field\">\n
				\t\t\t\t{fields}\n
				\t\t\t\t\t{field}{label}\n
				\t\t\t\t{fields}\n
				\t\t\t</div>\n
				\t\t</div>\n
			");
		}
		 */

		$properties = \Locomo\Model_Frmt_Element::properties();
		$config = \Config::load('form', true);

		foreach ($properties as $prop_name => $prop)
		{
			if ($form->field($prop_name) && isset($prop['unit']))
			{
				if ($form->field($prop_name)->template) {
					$form->field($prop_name)->set_template(str_replace('{field}', '{field}'.$prop['unit'], $form->field($prop_name)->template));
				} else {
					if (
						$form->field($prop_name)->type == 'text' ||
						$form->field($prop_name)->type == 'textarea' ||
						$form->field($prop_name)->type == 'select'
					)
					{
						$form->field($prop_name)->set_template(str_replace('{field}', '{field}'.$prop['unit'], $config['field_template']));
					}
					else if (
						$form->field($prop_name)->type == 'checkbox' ||
						$form->field($prop_name)->type == 'radio'
					)
					{
						$form->field($prop_name)->set_template(str_replace('{field}', '{field}'.$prop['unit'], $config['multi_field_template']));
					}
				}
			}
		}

		static::setModelProperties($model_properties, true);
		$form->add_after('modelPropertiesForm', '', array(), array(), 'txt')
			->set_template(static::modelPropertiesForm());

		return $form;
	}




	public static function modelPropertiesForm($model_properties = array())
	{
		if ($model_properties) static::setModelProperties($model_properties);

		$html = '<ul id="model_properties">';
		// モデルのプロパティを追加
		foreach (static::$_model_properties as $prop_name => $label)
		{
			$html .= '<li>';
			$html .= '<span class="label_name">'.$label.'</span>';
			$html .= '<button name="field_'.$prop_name.'" id="form_field_'.$prop_name.'" title="'.$prop_name.'" data-field="'.$prop_name.'" class="field_'.$prop_name.' btn small add_txt" value="'.$label.'" type="button">データに追加</button>';
			$html .= '<button name="field_'.$prop_name.'" id="form_field_'.$prop_name.'" title="'.$prop_name.'" data-field="'.$prop_name.'" class="field_'.$prop_name.' btn small add_row" value="'.$label.'" type="button">新規列として追加</button>';
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

