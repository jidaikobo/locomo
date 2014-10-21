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

class Sort {

	protected static $label = '';
	public static function sort($field, $label) {

		
		$input_get = \Input::get();
		if (isset($input_get['orders'][$field])) {
			switch($input_get['orders'][$field]) {
				case 'asc':
					$input_get['orders'][$field] = 'desc';
					$class = 'asc';
					break;
				case 'desc':
					unset($input_get['orders']);
					$class = 'desc';
					break;
				default:
					$input_get['orders'][$field] = 'asc';
					$class = '';
					break;
			}
		} else {
			unset($input_get['orders']);
			$input_get['orders'][$field] = 'asc';
			$class = '';
		}

		$url = \Uri::create(\Uri::current(), array(), $input_get);
		return \Html::anchor($url, $label ?: $field, array('class' => $class));
	}


	public static function sort_info($model) {
		if ( is_null( \Input::get('orders')) ) {
			return null;
		}

		$field = array_keys( \Input::get('orders'))[0];
		$sort = \Input::get('orders')[$field];

		if ($model::primary_key()[0] == $field) {
			$label = 'ID';
		} else {
			$form = $model::form_definition('fack');
			if ($form->field($field)) {
				$label = $form->field($field)->label;
			} else {
				$label = $field;
			}
		}

		if ($sort == 'asc') {
			return $field . 'を昇順で並べ替えています。';
		} else {
			return $field . 'を降順で並べ替えています。';
		}

	}
}
