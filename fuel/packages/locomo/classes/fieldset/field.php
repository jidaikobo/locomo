<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Locomo
 * @version    0.1
 * @author     otegami@tsukitsume.com
 * @license    MIT License
 * @link       http://tsukitsume.com
 */

namespace Locomo;



/**
 * Fieldset Class
 *
 * Define a set of fields that can be used to generate a form or to validate input.
 *
 * @package   Fuel
 * @category  Core
 */
class Fieldset_Field extends \Fuel\Core\Fieldset_Field
{
	public function get_value()
	{
		return $this->value;
	}

	public function get_name() {
		return $this->name;
	}

	public function set_name($name) {
		$this->name = $name;
	}


	/*
	 * alike a build method
	 * input タグ を出力しない
	 * @param 無視する $dispose_build_plain
	 */
	public function build_plain($dispose_build_plain = null, $date_format = null) {

		if (substr($this->name, -2) == 'at' and !is_null($date_format)) {
			// 時間の表記を含む なおかつ 00:00:00
			if (\Arr::filter_keys( array_flip(str_split($date_format)), str_split('aABgGhHisur')) and
				strtotime(substr($this->value, -8)) == strtotime('00:00:00')
			) {
				foreach (str_split('aABgGhHisur') as $s) { 
					if (strpos($date_format, $s)) $str_pos[] = strpos($date_format, $s);
				}
				$date_format = substr($date_format, 0, min($str_pos));
			}
			if ($this->value != '0000-00-00 00:00:00'){
				return $this->template_plain(date($date_format, strtotime($this->value)));
			} else {
				return $this->template_plain('');
			}
		}

		if (!empty($this->options)) {
			!is_array($this->value) and $this->value = array($this->value);
			$val = \Arr::filter_keys($this->options, $this->value);
			return $this->template_plain($val);
		} else {
			return $this->template_plain($this->value);
		}
	}


	protected function template_plain($build_field)
	{
		$form = $this->fieldset()->form();

		// $required_mark = $this->get_attribute('required', null) ? $form->get_config('required_mark', null) : null;
		$label = '<label class="">' . $this->label . '</label>';

		if (is_array($build_field))
		{
			$label = $this->label ? str_replace('{label}', $this->label, $form->get_config('group_label', '<span>{label}</span>')) : '';
			$template = $this->template ?: $form->get_config('form.multi_field_template_plain', "\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}\n{fields}\t\t\t\n\t\t\t</td>\n\t\t</tr>\n");
			if ($template && preg_match('#\{fields\}(.*)\{fields\}#Dus', $template, $match) > 0)
			{
				$build_fields = '';
				foreach ($build_field as $lbl => $bf)
				{
					$bf_temp = str_replace('{label}', '', $match[1]);
					$bf_temp = str_replace('{required}', '', $bf_temp);
					$bf_temp = str_replace('{field}', $bf, $bf_temp);
					$build_fields .= $bf_temp;
				}

				$template = str_replace($match[0], '{fields}', $template);
				$template = str_replace(array('{group_label}', '{required}', '{fields}', '{error_msg}', '{error_class}', '{description}'), array($label, '', $build_fields, '', '', ''), $template);

				return $template;
			}

			// still here? wasn't a multi field template available, try the normal one with imploded $build_field
			$build_field = implode(' ', $build_field);
		}

		// determine the field_id, which allows us to identify the field for CSS purposes
		$field_id = 'col_'.$this->name;
		if ($parent = $this->fieldset()->parent())
		{
			$parent->get_tabular_form() and $field_id = $parent->get_tabular_form().'_col_'.$this->basename;
		}

		$template = $this->template ?: $form->get_config('field_template_plain', "\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{field}</td>\n\t\t</tr>\n");
		$template = str_replace(array('{label}', '{field}', '{field_id}'),
			array($label, $build_field, $field_id),
			$template);

		$template = str_replace(array('{label}', '{required}', '{field}', '{error_msg}', '{error_class}', '{description}', '{field_id}'),
			array($label, '', $build_field, '', '', '', ''),
			$template);


		return $template;
	}
}

