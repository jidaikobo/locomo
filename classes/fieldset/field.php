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


	public function __set($name, $value) {
		if ($name == 'name') $this->name = $value;
	}

	// バグ
	public function delete_rule($callback, $set_attr = true)
	{
		foreach($this->rules as $index => $rule)
		{
			if ($rule[0] === $callback)
			{
				unset($this->rules[$index]);
				break;
			}
		}

		if ($callback === 'required' or $callback === 'required_with')
		{
			unset($this->attributes[$callback]);
		}

		return $this;
	}





	/*
	 * 暗黙的ラベル用にオーバーライド
	 */
	public function build() {
		// if ($this->tabular_form_relation) return parent::build();

		$form = $this->fieldset()->form();
		if ($this->error() and $form->get_config('error_alert_link')) {
			$this->set_attribute('data-jslcm-tooltip',"{error_msg}");
		} else {
			// todo template 書き換え
			$this->template('data-jslcm-tooltip',"{error_msg}");
		}


		// title 要素に label 説明文 エラー を表示
		if ($title_contained = $form->get_config('title_contents')) {
			if ($this->type == "text"
				or $this->type == "testarea"
			) {
				if (!$this->get_attribute('title')) {
					$title_inner = ''; // タイトル 説明文 エラー
					if (is_array($title_contained)) {
						foreach ($title_contained as $tc) {
							if ($tc == 'error') {
								$title_inner .= (string)$this->error() . ' ';
							} elseif($tc == 'description') {
								$title_inner .= (string)$this->description . ' ';
							} else {
								$title_inner .= (string)$this->get_attribute($tc) . ' ';
							}
						}
					} else {
						$title_inner .= (string)$this->get_attribute('label') . ' ';
						$title_inner .= (string)$this->error() . ' ';
						$title_inner .= (string)$this->description . ' ';
					}
					$this->set_attribute('title', $title_inner);
				}
			} elseif (
				$this->options and
				$this->type == "checkbox"
				or $this->type == "radio") {
				$template = $this->template ?: $form->get_config('multi_field_template');
				if (!$template) throw new \RuntimeException('title_contents を設定する時は title="{title_contents}" と class="example {type}" を含めた multi_field_template を config from.php で設定して下さい');
					$title_inner = ''; // タイトル 説明文 エラー
					if (is_array($title_contained)) {
						foreach ($title_contained as $tc) {
						foreach ($title_contained as $tc) {
							if ($tc == 'error') {
								$title_inner .= (string)$this->error() . ' ';
							} elseif($tc == 'description') {
								$title_inner .= (string)$this->description . ' ';
							} else {
								$title_inner .= (string)$this->get_attribute($tc) . ' ';
							}
						}
						}
					} else {
						$title_inner .= (string)$this->get_attribute('label') . ' ';
						$title_inner .= (string)$this->error() . ' ';
						$title_inner .= (string)$this->get_attribute('description') . ' ';
					}
					$this->set_template(str_replace(
						array('{title_contents}', '{type}'),
						array($title_inner, $this->type),
					$template));
			}
		} 
		return parent::build();
	}




	/*
	 * 暗黙的ラベル用にオーバーライド
	 */
	protected function template($build_field)
	{
		$form = $this->fieldset()->form();

		$required_mark = $this->get_attribute('required', null) ? $form->get_config('required_mark', null) : null;
		$label = $this->label ? $form->label($this->label, null, array('id' => 'label_'.$this->name, 'for' => $this->get_attribute('id', null), 'class' => $form->get_config('label_class', null))) : '';
		$error_template = $form->get_config('error_template', '');
		$error_msg = ($form->get_config('inline_errors') && $this->error()) ? str_replace('{error_msg}', $this->error(), $error_template) : '';
		$error_class = $this->error() ? $form->get_config('error_class') : '';

		if (is_array($build_field))
		{
			$label = $this->label ? str_replace('{label}', $this->label, $form->get_config('group_label', '<span>{label}</span>')) : '';
			$template = $this->template ?: $form->get_config('multi_field_template_plain',
				$form->get_config(
					'multi_field_template',
					"\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}<br />\n{fields}\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n")
			);
			if ($template && preg_match('#\{fields\}(.*)\{fields\}#Dus', $template, $match) > 0)
			{
				$build_fields = '';
				foreach ($build_field as $lbl => $bf)
				{

					// 追加1 暗黙的ラベル ここから
					if ((bool)$form->get_config('implicit_label', false)) {

						if (mb_strpos($match[1], 'field') < mb_strpos($match[1], 'label')) { // field が前

							$lbl = str_replace('</label>', '', strval($lbl));

							// $lbl_text = substr( $lbl, mb_strpos($lbl, '>')+1, mb_strlen($lbl)); だめみたい事例用においておく
							$lbl_text = substr( $lbl, mb_strpos($lbl, '>')+1);
							$lbl = str_replace($lbl_text, '', $lbl);
							$bf_temp = str_replace('{field}', $lbl, $match[1]);
							$bf_temp = str_replace('{required}', $required_mark, $bf_temp);
							$bf_temp = str_replace('{label}', $bf . $lbl_text . '</label>', $bf_temp);

						} elseif (strpos($match[1], 'field') > strpos($match[1], 'label')) { // label が前

							$lbl = str_replace('</label>', '', $lbl);

							$bf_temp = str_replace('{label}', $lbl, $match[1]);
							$bf_temp = str_replace('{required}', $required_mark, $bf_temp);
							$bf_temp = str_replace('{field}', $bf . '</label>', $bf_temp);

						}

					} else {
					// 追加1 暗黙的ラベル ここまで

						$bf_temp = str_replace('{label}', $lbl, $match[1]);
						$bf_temp = str_replace('{required}', $required_mark, $bf_temp);
						$bf_temp = str_replace('{field}', $bf, $bf_temp);
					}

					$build_fields .= $bf_temp;
				}

				$template = str_replace($match[0], '{fields}', $template);
				// 追加 3
				$error_alert_link = $this->error() ? $form->get_config('error_alert_link') : '';

				// 変更 error_alert_link を足した
				$template = str_replace(
					array('{group_label}', '{required}', '{fields}', '{error_msg}', '{error_class}', '{description}', '{error_alert_link}'),
					array($label, $required_mark, $build_fields, $error_msg, $error_class, $this->description, $error_alert_link), $template
				); // 変更

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

		$template = $this->template ?: $form->get_config('field_template', "\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{field} {description} {error_msg}</td>\n\t\t</tr>\n");

		/*
		// 追加 2 ここから
		if ((bool)$form->get_config('implicit_label', false) and $this->type == 'checkbox') {
			$label = str_replace('</label>', '', $label);
			$build_field .= '</label>';
		}
		// 追加 2 ここまで
		 */


		 // 追加 3
		$error_alert_link = $this->error() ? $form->get_config('error_alert_link') : '';

		// $build_field->set_attribute('data-jslcm-tooltip', $error_msg);


		// 変更 error_alert_link を足した
		$template = str_replace(array('{label}', '{required}', '{field}', '{error_msg}', '{error_class}', '{description}', '{field_id}', '{error_alert_link}'),
			array($label, $required_mark, $build_field, $error_msg, $error_class, $this->description, $field_id , $error_alert_link),
			$template);

		return $template;
	}




/* ========================================
 * 以下 build plain
 ======================================== */
	/*
	 * alike a build method
	 * input タグ を出力しない
	 */
	public function build_plain() {
		$form = $this->fieldset()->form();

		// Add IDs when auto-id is on
		if ($form->get_config('auto_id', false) === true and $this->get_attribute('id') == '')
		{
			$auto_id = $form->get_config('auto_id_prefix_plain', $form->get_config('auto_id_prefix', ''))
				.str_replace(array('[', ']'), array('-', ''), $this->name);
			$this->set_attribute('id', $auto_id);
		}



		switch( ! empty($this->attributes['tag']) ? $this->attributes['tag'] : $this->type)
		{
			case 'radio':
			case 'checkbox':
				if ($this->options)
				{
					$build_field = array();
					$i = 0;
					foreach ($this->options as $value => $label)
					{
						$attributes = $this->attributes;
						$attributes['name'] = $this->name;
						$this->type == 'checkbox' and $attributes['name'] .= '['.$i.']';

						$attributes['value'] = $value;
						$attributes['label'] = $label;

						if (is_array($this->value) ? in_array($value, $this->value) : $value == $this->value)
						{
							$attributes['checked'] = 'checked';
						}

						if( ! empty($attributes['id']))
						{
							$attributes['id'] .= '_'.$i;
						}
						else
						{
							$attributes['id'] = null;
						}
						$build_field[$form->label($label, null, array('for' => $attributes['id']))] = $this->type == 'radio'
							? $form->radio($attributes)
							: $form->checkbox($attributes);

						$i++;
					}
				}
				else
				{
					$build_field = $this->value;
				}
			break;

			case 'select':
				$attributes = $this->attributes;
				// $name = $this->name;
				// unset($attributes['type']);
				!is_array($this->value) and $this->value = array($this->value);
				$val = implode(\Arr::filter_keys($this->options, $this->value), ', ');
				$build_field = $val;
			break;

			case 'textarea':
				$attributes = $this->attributes;
				unset($attributes['type']);
				$build_field = $this->value;
			break;

			case 'button':
				$build_field = $this->value;
			break;

			case 'hidden':
			case false:
			case 'submit':
				return '';
			break;

			default:
				$build_field = $this->value;
			break;
		}

		// if (empty($build_field)) var_dump ($this->name) ;
		if ($this->type == 'hidden')
		{
			return $build_field;
		}

		// _at を $date_format の形に
		if (substr($this->name, -2) == 'at') {
			// 時間の表記を含む なおかつ 00:00:00

			$date_format = $form->get_config('date_format_plain', 'Y-m-d H:i:s');
			if (\Arr::filter_keys( array_flip(str_split($date_format)), str_split('aABgGhHisur')) and
				date('His', strtotime($this->value)) == date('His', strtotime('00:00:00'))
			) {
				foreach (str_split('aABgGhHisur') as $s) { 
					if (strpos($date_format, $s)) $str_pos[] = strpos($date_format, $s);
				}
				$date_format = substr($date_format, 0, min($str_pos));
			}
			if ($this->value != false and $this->value != '0000-00-00 00:00:00' and $this->value != '0000-00-00'){
				$build_field = date($date_format, strtotime($this->value));
			} else {
				$build_field = '';
			}
		}

		$label = $this->label ? $form->label($this->label, null, array('id' => 'label_'.$this->name, 'for' => $this->get_attribute('id', null), 'class' => $form->get_config('label_class', null))) : '';
		// var_dump($label);
		return $this->template_plain($build_field, $label);




		if (!empty($this->options)) {
			!is_array($this->value) and $this->value = array($this->value);
			$val = \Arr::filter_keys($this->options, $this->value);
			return $this->template_plain($val);
		} else {
			return $this->template_plain($this->value);
		}
	}


	/*
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
	 */
	protected function template_plain($build_field, $label)
	{
		$form = $this->fieldset()->form();

		$required_mark = '';
		if ($label) {
		   	$label = $this->label ? $form->label($this->label, null, array('id' => 'label_'.$this->name, 'for' => $this->get_attribute('id', null), 'class' => $form->get_config('label_class', null))) : '';
		}
		$error_template = '';
		$error_msg = '';
		$error_class = '';

		if (is_array($build_field))
		{
			$label = $this->label ? str_replace('{label}', $this->label, $form->get_config('group_label', '<span>{label}</span>')) : '';
			$template = $this->template ?: $form->get_config('multi_field_template_plain',
				 $form->get_config(
					'multi_field_template',
					"\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}<br />\n{fields}\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n"
				)
			);
			if ($template && preg_match('#\{fields\}(.*)\{fields\}#Dus', $template, $match) > 0)
			{
				/*
				foreach ($build_field as $lbl => $bf)
				{
					$bf_temp = str_replace('{label}', $lbl, $match[1]);
					$bf_temp = str_replace('{required}', $required_mark, $bf_temp);
					$bf_temp = str_replace('{field}', $bf, $bf_temp);
					$build_fields .= $bf_temp;
				}
				 */
				!is_array($this->value) and $this->value = array($this->value);
				$val = \Arr::filter_keys($this->options, $this->value);
				// var_dump($val);
				$build_fields = implode($val, ', ');

				$template = str_replace($match[0], '{fields}', $template);
				$template = str_replace(
					array('{group_label}', '{required}', '{fields}', '{error_msg}', '{error_class}', '{description}', '{error_alert_link}'),
					array($label, $required_mark, $build_fields, $error_msg, $error_class, $this->description, ''),
					$template
				);

				return $template;
			}

			// still here? wasn't a multi field template available, try the normal one with imploded $build_field
			$build_field = implode(' ', $build_field);
		}

		// determine the field_id, which allows us to identify the field for CSS purposes
		$field_id = 'col_'.$this->name;
		$field_template_plain = 'field_template_plain'; // 追加
		if ($parent = $this->fieldset()->parent())
		{
			$parent->get_tabular_form() and $field_id = $parent->get_tabular_form().'_col_'.$this->basename;
			$parent->get_tabular_form() and $field_template_plain = 'field_template'; // 追加
		}

		$template = $this->template ?: $form->get_config($field_template_plain,
			$form->get_config(
				'field_template',
				"\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{field} {description} {error_msg}</td>\n\t\t</tr>\n"
			)
		);
		$template = str_replace(array('{label}', '{required}', '{field}', '{error_msg}', '{error_class}', '{description}', '{field_id}', '{error_alert_link}', '{auto_id}'),
			array($label, $required_mark, $build_field, $error_msg, $error_class, '', $field_id, '', $this->get_attribute('id')),
			$template);

		return $template;
	}




}
