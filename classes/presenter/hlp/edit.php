<?php
class Presenter_Hlp_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

		// action
		$action = urlencode(\Input::get('action'));

		// prepare options
		$actions = array('all' => '共通ヘルプ');
		$controllers = array();
		foreach(\Util::get_mod_or_ctrl() as $k => $v)
		{
			if ( ! isset($v['nicename']) || ! isset($v['main_action'])) continue;
			if ( ! \Util::get_locomo($k, 'nicename')) continue;
			$controllers[\Inflector::ctrl_to_safestr($k)] = \Util::get_locomo($k, 'nicename');
		}
		$selected = isset($obj->ctrl) && ! empty($obj->ctrl) ? $obj->ctrl : $action;
		$form->field('ctrl')
			->set_options($controllers)
			->set_value($selected);

		//title
		$title = \Arr::get($controllers, $selected, @$obj->title);
		$form->field('title')
			->set_value($title);

		return $form;
	}
}
