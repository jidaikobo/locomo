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
				// todo 複数のfieldの処理
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
			if ($f instanceof \Fieldset_Field) $f->set_name($str . '[' . $f->get_name() . ']');
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


	/**
	 * Enable or disable the tabular form feature of this fieldset
	 *
	 * @param  string  Model on which to define the tabular form
	 * @param  string  Relation of the Model on the tabular form is modeled
	 * @param  array  Collection of Model objects from a many relation
	 * @param  int  Number of empty rows to generate
	 *
	 * @return  Fieldset  this, to allow chaining
	 */
	/*
	public function set_bulk_form($model, $relation, $parent, $blanks = 1)
	{
		// make sure our parent is an ORM model instance
		if ( ! $parent instanceOf \Orm\Model)
		{
			throw new \RuntimeException('Parent passed to set_tabular_form() is not an ORM model object.');
		}

		// validate the model and relation
		// fetch the relations of the parent model
		$relations = call_user_func(array($parent, 'relations'));
		if ( ! array_key_exists($relation, $relations))
		{
			throw new \RuntimeException('Relation passed to set_tabular_form() is not a valid relation of the ORM parent model object.');
		}

		// check for compound primary keys
		try
		{
			// fetch the relations of the parent model
			$primary_key = call_user_func($model.'::primary_key');

			// we don't support compound primary keys
			if (count($primary_key) !== 1)
			{
			throw new \RuntimeException('set_tabular_form() does not supports models with compound primary keys.');
			}

			// store the primary key name, we need that later
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

		// add the rows to the tabular form fieldset
		foreach ($parent->{$relation} as $row)
		{
			// add the row fieldset to the tabular form fieldset
			$this->add($fieldset = \Fieldset::forge($this->tabular_form_relation.'_row_'.$row->{$primary_key}));

			// and add the model fields to the row fielset
			$fieldset->add_model($model, $row)->set_fieldset_tag(false);
			$fieldset->set_config(array(
				'form_template' => \Config::get('form.tabular_row_template', "<table>{fields}</table>\n"),
				'field_template' => \Config::get('form.tabular_row_field_template', "{field}")
			));
			$fieldset->add($this->tabular_form_relation.'['.$row->{$primary_key}.'][_delete]', '', array('type' => 'checkbox', 'value' => 1));
		}

		// and finish with zero or more empty rows so we can add new data
		if ( ! is_numeric($blanks) or $blanks < 0)
		{
			$blanks = 1;
		}
		for ($i = 0; $i < $blanks; $i++)
		{
			$this->add($fieldset = \Fieldset::forge($this->tabular_form_relation.'_new_'.$i));
			$fieldset->add_model($model)->set_fieldset_tag(false);
			$fieldset->set_config(array(
				'form_template' => \Config::get('form.tabular_row_template', "<tr>{fields}</tr>"),
				'field_template' => \Config::get('form.tabular_row_field_template', "{field}")
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
*/


	/*
	 * 全リフレッシュ用の tabular
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
				'form_template' => \Config::get('form.tabular_row_template_blank', "<tr class=\"{$this->tabular_form_relation}{$i}\">{fields}</tr>"),
				'field_template' => \Config::get('form.tabular_row_field_template', "{field}")
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
	 * @param bool|array  $build_field array('form_name' => array(output values)))
	 * @param str         $date_format
	 * @return  string
	 */
	public function build_plain($build_field = false, $date_format = null)
	{

		$fields_output = '';

		// construct the tabular form table header
		if ($this->tabular_form_relation)
		{
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

			// リレーションされたフィールド
			// todo mm のセレクトなど
			if ($f instanceof \Fieldset_Field) {

				if (is_array($build_field) and isset($build_field[$this->name])) {
					var_dump($this->name);
						if (in_array($f->name, $build_field[$this->name])) {
							if (!$f->type or $f->type == 'hidden') continue;
							!in_array($f->name, $this->disabled) and $fields_output .= $f->build_plain($build_field, $date_format).PHP_EOL; // Fieldset->build_plain or Fieldset_field->build_plain()
					}

				} elseif (!$build_field or !is_array($bild_field[$this->name])) {
					if (!$f->type or $f->type == 'hidden') continue;
					!in_array($f->name, $this->disabled) and $fields_output .= $f->build_plain($build_field, $date_format).PHP_EOL; // Fieldset->build_plain or Fieldset_field->build_plain()
				}

			}
		}

		/*
		$close = ($this->fieldset_tag == 'form' or empty($this->fieldset_tag))
			? $this->form()->close($attributes).PHP_EOL
			: $this->form()->{$this->fieldset_tag.'_close'}($attributes);
		 */

		$template = $this->form()->get_config('form_template_plain', "\n\t\t<table class=''>\n{fields}\n\t\t</table>\n");

		$output = str_replace(array('{fields}'), array($fields_output), $template);

		return $output;
	}



	public function set_tabular_form_plain($model, $relation, $parent)
	{
		if ( ! $parent instanceOf \Orm\Model) throw new \RuntimeException('Parent passed to set_tabular_form() is not an ORM model object.');
		$relations = call_user_func(array($parent, 'relations'));
		if ( ! array_key_exists($relation, $relations)) throw new \RuntimeException('Relation passed to set_tabular_form() is not a valid relation of the ORM parent model object.');

		// check for compound primary keys
		try
		{
			// fetch the relations of the parent model
			$primary_key = call_user_func($model.'::primary_key');

			// we don't support compound primary keys
			if (count($primary_key) !== 1) throw new \RuntimeException('set_tabular_form() does not supports models with compound primary keys.');

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

		\Config::load('form', true);

		$this->set_config(array(
			'form_template_plain' => \Config::get('form.tabular_form_template_plain', "<table>{fields}</table>\n"),
			'field_template_plain' => \Config::get('form.tabular_field_template_plain', "{field}")
		));

		// add the rows to the tabular form fieldset
		foreach ($parent->{$relation} as $row)
		{
			$this->add($fieldset = \Fieldset::forge($this->tabular_form_relation.'_row_'.$row->{$primary_key}));

			$fieldset->add_model($model, $row)->set_fieldset_tag(false);
			$fieldset->set_config(array(
				'form_template_plain' => \Config::get('form.tabular_row_template_plain', "<table>{fields}</table>\n"),
				'field_template_plain' => \Config::get('form.tabular_row_field_template_plain', "{field}")
			));
		}

		return $this;
	}

	public function delete($field) {
		if (is_array($field) ) {
			foreach($field as $f) {
				unset($this->fields[$f]);
			}
		} elseif (is_string($field)) {
			unset($this->fields[$field]);
		}
		return $this;
	}


}
