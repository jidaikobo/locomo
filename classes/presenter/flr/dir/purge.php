<?php
namespace Locomo;
class Presenter_Flr_Dir_Purge extends \Presenter_Base
{
	/**
	 * form()
	 */
	public static function form($obj = NULL)
	{
		$form = parent::form($obj);

		// form
		$form->add_after(
			'display_name',
			'ディレクトリ名',
			array('type' => 'text', 'disabled' => 'disabled', 'style' => 'width:90%;'),
			array(),
			'name'
		)->set_value(@$obj->name);

		// delete
		$form->delete('is_sticky');
		$form->delete('name');
		$form->delete('explanation');

		// back
		$back = \Html::anchor(\Uri::create('flr/index_files/'.$obj->id), '戻る', array('class' => 'button'));
		$form->field('submit')->set_value('完全に削除する')->set_template('<div class="submit_button">'.$back.'{field}</div>');

		return $form;
	}
}
