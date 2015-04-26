<?php
class Presenter_Msgbrd_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

		// categories
		$options = array('' => '選択してください');
		$options+= \Model_Msgbrd_Categories::find_options('name', array('where' => array(array('is_available', 1)), 'order_by' => array('seq' => 'ASC', 'name' => 'ASC')));
		$form->field('category_id')
			->set_options($options)
			->set_value($obj->category_id);

		return $form;
	}
}
