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

	public static function __callStatic($name, $arguments)
	{
		if ($name == 'create_nav') {
			if ($instance = static::instance() and method_exists($instance, 'render_nav'))
			{
				return call_fuel_func_array(array($instance, 'render_nav'), $arguments);
			}
		}

		return parent::__callStatic($name, $arguments);
	}

	public function render_nav($raw = false)
	{
		var_dump($this->current_page);
		// no links if we only have one page
		if ($this->config['total_pages'] == 1)
		{
			return $raw ? array() : '';
		}

		$this->raw_results = array();

		$total_strlen = strlen((string)$this->total_items);
		$input = $this->input ?: \Form::input('paged', '', array('id' => 'pagination_input', 'size' => $total_strlen));


		$form_open = $form_close = $wrap_start = $wrap_end = '';
		// if (is_string($this->uri_segment)) {
		if (true) {
			$form_open  = \Form::open(array('action' => \Uri::create(\Uri::current()), 'method' => 'get', 'class' => "search"));
			$form_close = \Form::close();
		} else {
			// $wrap_start = '<div class="pagination_wrap">';
		}

		var_dump($this->first());


		$html = $wrap_start;
		$html .= str_replace(
			'{pagination}',
			$form_open
			.$this->first().$this->previous().$input.'/'.$this->total_pages.$this->next().$this->last()
			.$form_close,
			$this->template['wrapper']
		);
		$html .= $wrap_end;


		return $raw ? $this->raw_results : $html;
	}



}

