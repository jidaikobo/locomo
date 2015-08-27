<?php
namespace Locomo;
class Presenter_Frmt_Pdf_Edit_Element extends \Presenter_Base
{
	public function view()
	{
		$this->setElements = function ($elements = null)
		{
			return static::setElements($elements);
		};

		$this->templateElement = function ()
		{
			return static::setElements(\Locomo\Model_Frmt_Element::forge());
		};

		$this->setController = function ($model_properties = array())
		{
			return static::setController($model_properties);
		};
	}

	public static function setElements($elements)
	{
		if (! $elements) return false;
		$result_str = '';
		$props = \Locomo\Model_Frmt_Element::properties();
		if (! is_array($elements)) $elements = array($elements);
		foreach ($elements as $element)
		{
			$form_id = $element->id ? 'element_' . $element->id : 'element_new_$';
			if (isset($element->form_key)) $form_id = str_replace('$', $element->form_key, $form_id);
			$form = \Fieldset::forge($form_id);
			// $form->add_model($element)->populate($element);
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
			$form = '
				<div class="display_name"></div>
				<div class="text_wrapper" >
					<div class="text">'.$element->txt.'</div>
				</div>' . $form;

			$result_str .= html_tag('div', array(
				'id' => $form_id,
				'class' => 'element'
			), $form);
		}
		return $result_str;
	}

	public static function setController($model_properties = array())
	{
		$form = \Fieldset::forge('controller');
		$form->add_model(\Locomo\Model_Frmt_Element::forge());

		$field_no_label = array(
			'ln_y',
			'h_adjustable',
		);
		/*
		$form->field('name')->set_template('opener');
		$form->field('')->set_template('opener');
		 */
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

		$form->field('border_left')->set_template("{opener}
				\t\t<div class=\"input_group lcm_focus label_fb {type}\" tabindex=\"0\" title=\"{title_contents} {error_msg}\" data-jslcm-tooltip=\"{error_msg}\">\n
				\t\t\t<div class=\"field\">{fields}\n
				\t\t\t\t{field}{label}\n
				\t\t\t{fields}\n
			");
		$form->field('border_top')->set_template("
			\t\t\t{fields}\n
			\t\t\t\t{field}{label}\n
			\t\t\t{fields}\n
		");
		$form->field('border_right')->set_template("
			\t\t\t{fields}\n
			\t\t\t\t{field}{label}\n
			\t\t\t{fields}\n
		");
		$form->field('border_bottom')->set_template("
			\t\t\t{fields}\n
			\t\t\t\t{field}{label}\n
			\t\t\t{fields}\n
			\t\t{error_alert_link}</div></div>\n
		{closer}");

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
		$form->add_after(static::modelPropertiesForm(), '', array(), array(), 'txt');

		return $form;
	}

	/*
	 * モデルのプロパティを追加
	 */
	protected static function modelPropertiesForm()
	{
		$form = \Fieldset::forge('model_properties_form');

		$form->add('model_properties_opener', '', array(), array())->set_template('<div id="model_properties">');

		$child_group_count = 0;
		foreach (static::$_model_properties as $prop_name => $label)
		{
			if (is_array($label))
			{
				$form->add('model_properties_group_opener_'.$child_group_count)->set_template('<fieldset class="model_properties_group">');
				$form->add('model_properties_group_legend_'.$child_group_count)->set_template('<legend>'.$prop_name.'</legend>');
				foreach ($label as $prop_name_c => $label_c)
				{
					$name = 'field_'.$prop_name_c;
					$form->add($name, '', array('type' => 'button', 'value' => $label_c, 'title' => $prop_name_c, 'class' => 'field_'.$prop_name_c.' btn small', 'data-field' => $prop_name_c))->set_template('{field}');
				}
				$form->add('model_properties_group_closer_'.$child_group_count)->set_template('</fieldset>');
				$child_group_count++;
			}
			else
			{
				$name = 'field_'.$prop_name;
				$form->add($name, '', array('type' => 'button', 'value' => $label, 'title' => $prop_name, 'class' => 'field_'.$prop_name.' btn small', 'data-field' => $prop_name))->set_template('{field}');
			}
		}
		$form->add('model_properties_closer', '', array(), array())->set_template('</div>');

		return $form;
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
