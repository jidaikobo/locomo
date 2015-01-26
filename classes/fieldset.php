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

class Fieldset extends \Fuel\Core\Fieldset
{

	/*
	 * \Fieldset add_model のラッパー
	 * $class (第一引数) が array の時の想定する
	 */
	public function add_model($class, $instance = null, $method = 'set_form_fields')
	{
		if (is_array($class)) {
			// todo why array_reverse??
			foreach (array_reverse($class) as $val) {
				parent::add_model($val, $instance, 'set_form_fields');
			}
			return $this;
		} else {
			return parent::add_model($class, $instance, $method);
		}
	}


	/*
	 * delete
	 */
	public function delete($field) {
		// tabular_form の delete
		// 先に set_tabular_form が thead を描画するため、使えない
		/*
		if ($this->tabular_form_relation) {
			$this->delete_tabular($field);
			return $this;
		}
		 */

		if (is_array($field) ) {
			foreach($field as $f) {
				unset($this->fields[$f]);
			}
		} elseif (is_string($field)) {
			unset($this->fields[$field]);
		}
		return $this;
	}



	/*
	 * tabular_form のテンプレートをフィールド毎に設定する
	 * 本来 set_template は Fieldset_Field クラスのメソッド。まとめて指定する為にこちらで定義する
	 * todo フィールド名を指定しない時の振る舞いを実装する
	 */
	public function set_tabular_form_template($template, $field = null) {

		$tabular_field = $this->field();
		foreach ($tabular_field as $k => $v) {
			if (is_string($field)) {
				$f = $v->field(str_replace('_row_', '[', $k) . '][' . $field . ']');
				// 新規列に対応
				if (!$f) $f = $v->field(str_replace('_new_', '_new[', $k) . '][' . $field . ']');
				//var_dump($v);
				if ($f) $f->set_template($template);
			} elseif (is_array($field)) {
				foreach ($field as $f_key => $f_val) {
					$f = $v->field(str_replace('_row_', '[', $k) . '][' . $f_val . ']');
					// 新規列に対応
					if (!$f) $f = $v->field(str_replace('_new_', '_new[', $k) . '][' . $f_val . ']');
					//var_dump($v);
					if ($f) $f->set_template($template);
				}
			} else {
				// todo デフォルトで全てのフィールドに適用する
				// 不具合懸念有り
			}
		}
		return $this;
	}


	/*
	 * Fieldset class の input name を配列形式にする
	 */
	public function set_input_name_array($str = null) {
		if (!empty($this->fieldset_children)) return;

		if (!$str) $str = $this->name;
		foreach ($this->field() as $f) {
			if ($f instanceof \Fieldset_Field) $f->name = $str . '[' . $f->name . ']';
		}
	}

	/*
	 * set_input_name_array をbuild時に行う
	 */
	public function build_cascade($action = null) {
		// $model_name = 'Model_' . ucfirst($this->name);
		// if (!class_exists($model_name)) $model_name = get_class($this->validation()->callables()[0]);
		// if (!class_exists($model_name)) throw new \InvalidArgumentException('Not found Model. set second param Model name.');;

		foreach($this->fieldset_children as $key => $child) {
			$child->set_input_name_array();
		}

		return parent::build($action);
	}




	/*
	 * 全リフレッシュ用の tabular
	 * 全て _new_ で set するため、delete と併用する
	 */
	public function set_tabular_form_blank($model, $relation, $parent, $blanks = 1)
	{
		if ( ! $parent instanceOf \Orm\Model) throw new \RuntimeException('Parent passed to set_tabular_form() is not an ORM model object.');

		$relations = call_user_func(array($parent, 'relations'));
		if ( ! array_key_exists($relation, $relations)) throw new \RuntimeException('Relation passed to set_tabular_form() is not a valid relation of the ORM parent model object.');

		try
		{
			$primary_key = call_user_func($model.'::primary_key');
			if (count($primary_key) !== 1)
			{
			throw new \RuntimeException('set_tabular_form() does not supports models with compound primary keys.');
			}
			$primary_key = reset($primary_key);
		}
		catch (\Exception $e)
		{
			throw new \RuntimeException('Unable to fetch the models primary key information.');
		}

		// store the tabular form class name
		$this->tabular_form_model = $model;

		// and the relation on which we model the rows
		$this->tabular_form_relation = $relation;

		// load the form config if not loaded yet
		\Config::load('form', true);

		// load the config for embedded forms
		$this->set_config(array(
			'form_template' => \Config::get('form.tabular_form_template', "<table>{fields}</table>\n"),
			'field_template' => \Config::get('form.tabular_field_template', "{field}")
		));

		// 既に入力されている列
		$inputed = array();
		// add the rows to the tabular form fieldset
		foreach ($parent->{$relation} as $row)
		{
			$value = $row->to_array();
			// var_dump($value[$primary_key]);
			$value[$primary_key] = null;
			// var_dump($value[$primary_key]);
			$inputed[] = $model::forge()
				->set($value);
		}

		// and finish with zero or more empty rows so we can add new data
		if ( ! is_numeric($blanks) or $blanks < 0) $blanks = 1;

		$blanks = $blanks + count($parent->{$relation});
		for ($i = 0; $i < $blanks; $i++)
		{
			$this->add($fieldset = \Fieldset::forge($this->tabular_form_relation.'_new_'.$i));
			if (isset($inputed[$i])) {
				$fieldset->add_model($inputed[$i])->populate($inputed[$i], false)->set_fieldset_tag(false);
			} else {
				$fieldset->add_model($model)->set_fieldset_tag(false);
			}
			$fieldset->set_config(array(
				'form_template' => \Config::get('form.tabular_row_template_blank', \Config::get('form.tabular_row_template', "<tr class=\"{$this->tabular_form_relation}{$i}\">{fields}</tr>")),
				'field_template' => \Config::get('form.tabular_row_field_template_blank', \Config::get('form.tabular_row_field_template', "{field}"))
			));
			$fieldset->add($this->tabular_form_relation.'_new['.$i.'][_delete]', '', array('type' => 'checkbox', 'value' => 0, 'disabled' => 'disabled'));

			// no required rules on this row
			foreach ($fieldset->field() as $f)
			{
				$f->delete_rule('required', false)->delete_rule('required_with', false);
			}
		}

		return $this;
	}



	/*
	 * alike a build method
	 * input タグ を出力しない
	 * @return  string
	 */
	public function build_plain()
	{
		$attributes = $this->get_config('form_attributes');

		$fields_output = '';

		// construct the tabular form table header
		if ($this->tabular_form_relation)
		{

			// _delete フィールドを削除
			$tabular_name = $this->name;
			foreach ($this->children() as $v) {
				if (strpos($v->name, '_row_') > 0) {
					$field_name = str_replace('_row_', '[', $v->name) . '][_delete]';
					unset($v->fields[$field_name]);
				} else {
					$field_name = str_replace('_new_', '_new[', $v->name) . '][_delete]';
					unset($v->fields[$field_name]);
				}
			}


			$properties = call_user_func($this->tabular_form_model.'::properties');
			$primary_keys = call_user_func($this->tabular_form_model.'::primary_key');
			$fields_output .= '<thead><tr>'.PHP_EOL;
			foreach ($properties as $field => $settings)
			{
				if ((isset($settings['skip']) and $settings['skip']) or in_array($field, $primary_keys))
				{
					continue;
				}
				if (isset($settings['form']['type']) and ($settings['form']['type'] === false or $settings['form']['type'] === 'hidden'))
				{
					continue;
				}
				$fields_output .= "\t".'<th class="'.$this->tabular_form_relation.'_col_'.$field.'">'.(isset($settings['label'])?\Lang::get($settings['label'], array(), $settings['label']):'').'</th>'.PHP_EOL;

			}


			$fields_output .= '</tr></thead>'.PHP_EOL;
		}

		foreach ($this->field() as $f)
		{
			in_array($f->name, $this->disabled) or $fields_output .= $f->build_plain().PHP_EOL;

			/*
			// リレーションされたフィールド
			// todo mm のセレクトなど
			if ($f instanceof \Fieldset_Field) {

				if (is_array($action) and isset($action[$this->name])) {
					// var_dump($this->name);
						if (in_array($f->name, $action[$this->name])) {
							if (!$f->type or $f->type == 'hidden') continue;
							!in_array($f->name, $this->disabled) and $fields_output .= (string)$f->build_plain($action, $date_format).PHP_EOL; // Fieldset->build_plain or Fieldset_field->build_plain()
					}

				} elseif (!$action or !is_array($bild_field[$this->name])) {
					if (!$f->type or $f->type == 'hidden') continue;
					!in_array($f->name, $this->disabled) and $fields_output .= (string)$f->build_plain($action, $date_format).PHP_EOL; // Fieldset->build_plain or Fieldset_field->build_plain()
				}

			} else {
				in_array($f->name, $this->disabled) or $fields_output .= (string)$f->build_plain().PHP_EOL;
			}
			 */
		}

		/*
		$template = $this->form()->get_config('form_template_plain', "\n\t\t<table class=''>\n{fields}\n\t\t</table>\n");

		$output = str_replace(array('{fields}'), array($fields_output), $template);
		 */

		$template = $this->form()->get_config((empty($this->fieldset_tag) ? 'form_plain' : $this->fieldset_tag).'_template_plain',
			$this->form()->get_config(
				(empty($this->fieldset_tag) ? 'form' : $this->fieldset_tag).'_template',
				"\n\t\t{fields}\n"
			)
		);

		$template = str_replace(array('{form_open}', '{open}', '{fields}', '{form_close}', '{close}'),
			array('', '', $fields_output, '', ''),
			$template);



		return $template;
	}


	public function set_tabular_form($model, $relation, $parent, $blanks = 1)
	{
		if (\Request::main()->action == 'view') $blanks = 0;
		$tabular =  parent::set_tabular_form($model, $relation, $parent, $blanks);


		$_deletes = $tabular->tabular_field('_delete');
		$delete_label = \Config::get('form.tabular_delete_label_field', \Config::get('form.tabular_delete_label', 'Delete?'));
		foreach ($_deletes as $_delete) {
			if ($delete_label) $_delete->set_label($delete_label);
		}
		foreach ($tabular->children() as $child) {
			foreach ($child->field() as $f) {
				if ( ($f->type == 'checkbox' or $f->type == 'radio') and $f->options ) {
					$template = $this->form()->get_config('multi_field_template_tabular',
						 $this->get_config(
							'form.multi_field_template',
							"\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}<br />\n{fields}\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n"
						)
					);

					$f->set_template($template);
				}
			}
		}

		return $tabular;
	}

	/*
	 * @return array
	 */
	public function tabular_field($name = null) {
		if (!$this->tabular_form_relation) return false;

		$tabular_name = $this->name;
		$return = array();
		foreach ($this->children() as $v) {
			if (strpos($v->name, '_row_') > 0) {
				$field_name = str_replace('_row_', '[', $v->name) . '][' . $name . ']';
			} else {
				$field_name = str_replace('_new_', '_new[', $v->name) . '][' . $name . ']';
			}
			$return[] = $v->field($field_name);
		}

		return $return;
	}

	// 先に set_tabular_form が thead を描画するため、使えない
	/*
	public function delete_tabular($field = null) {
		if (!$this->tabular_form_relation) return false;
		if (!is_array($field)) $field = array($field);
		foreach($field as $f) {
			$tabular_name = $this->name;
			foreach ($this->children() as $v) {
				if (strpos($v->name, '_row_') > 0) {
					$field_name = str_replace('_row_', '[', $v->name) . '][' . $f . ']';
					unset($v->fields[$field_name]);
				} else {
					$field_name = str_replace('_new_', '_new[', $v->name) . '][' . $f . ']';
					unset($v->fields[$field_name]);
				}
			}
		}
		return $this;
	}
	 */



}
