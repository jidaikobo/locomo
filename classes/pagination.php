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

class Pagination extends \Fuel\Core\Pagination {

	public static function sort($field, $label, $reset = true) {

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

		$p_seg = static::instance()->config['uri_segment'];
		if ($reset) {
			if ($p_seg and is_int($p_seg)) {
				$url = \Uri::base();
				$segments = \Uri::segments();
				$p_seg--;
				for ($i = 0; $i < count($segments); $i ++) {
					if ($i == $p_seg) {
						$url .= '1/';
					} else {
						$url .= $segments[$i] . '/';
					}
				}
				$url = \Uri::create($url, array(), $input_get);
				return \Html::anchor($url, $label ?: $field, array('class' => $class));

			} elseif (is_string($p_seg)) {
				unset($input_get[$p_seg]);
			}
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

			if (isset($model::properties()[$field]['label'])) {
				$label = $model::properties()[$field]['label'];
			} else {
				$label = $field;
			}
		}

		if ($sort == 'asc') {
			return $label . 'を昇順で並べ替えています。';
		} else {
			return $label . 'を降順で並べ替えています。';
		}

	}

}

