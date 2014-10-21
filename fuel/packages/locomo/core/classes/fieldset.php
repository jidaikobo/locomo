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

namespace Locomo_Core;

class Fieldset extends \Fuel\Core\Fieldset {

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
		//die();

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


}
