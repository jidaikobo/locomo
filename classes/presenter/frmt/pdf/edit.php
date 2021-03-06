<?php
namespace Locomo;
class Presenter_Frmt_Pdf_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$config = \Config::load('form', true);

		$form = parent::form($obj);

		$properties = $obj::properties();
		$config = \Config::load('form', true);
		foreach ($properties as $prop_name => $prop)
		{
			if ($form->field($prop_name) && isset($prop['unit']))
			{
				if (
					$form->field($prop_name)->template &&
					$form->field($prop_name)->template != 'opener' &&
					$form->field($prop_name)->template != 'closer'
				) {
					$form->field($prop_name)->set_template(str_replace('{field}', '{field}'.$prop['unit'], $form->field($prop_name)->template));
				} else {
					$opener = $form->field($prop_name)->template == 'opener' ? '{opener}' : '';
					$closer = $form->field($prop_name)->template == 'closer' ? '{closer}' : '';
					if (
						$form->field($prop_name)->type == 'text' ||
						$form->field($prop_name)->type == 'textarea' ||
						$form->field($prop_name)->type == 'select'
					)
					{
						$form->field($prop_name)->set_template($opener . str_replace('{field}', '{field}'.$prop['unit'], $config['field_template']) . $closer);
					}
					else if (
						$form->field($prop_name)->type == 'checkbox' ||
						$form->field($prop_name)->type == 'radio'
					)
					{
						$form->field($prop_name)->set_template($opener . str_replace('{field}', '{field}'.$prop['unit'], $config['multi_field_template']) . $closer);
					}
				}
			}
		}

		$form->field('w')->set_template('<div class="lcm_form form_group" id="preview_left">' . $form->field('w')->template);

		$form->field('cell_w')->set_attribute('readonly', 'readonly');
		$form->field('cell_h')->set_attribute('readonly', 'readonly');

		// プレビュー用の領域を表示
		$form->field('space_vertical')->set_template($form->field('space_vertical')->template . '</div> <!-- // wrapper -->');
		$form->add_after('preview', '', array(), array(), 'space_vertical')->set_template('
			<div id="preview_right">
				<h2>印刷プレビュー</h2>
				<div id="preview">
					<div id="print">
					</div>
				</div>
			</div>
		');

		$form->add_after(
			'upload[]',
			'使用イメージのアップロード',
			array(
				'type' => 'file',
				'multiple' => 'multiple',
				'description' => '
					画像ファイル .jpeg .jpg .png .gif ファイル<br>
					複数ファイル可<br>
				',
				'class' => 'button',
			),
			array(),
			'preview');

		$images = (\File::get_attached_files($obj::$_upload_path, false, array('image')));
		$select = array();

		if ($images)
		{
			foreach ($images as $url => $filename)
			{
				$thumb_url = substr($url, 0, strrpos($url, '.')).'_tn.jpg';// substr($url, strrpos($url, '.'));
				$select[$url] = \Html::img($thumb_url) . $filename;
			}
		}
		if ($select)
		{
			$form->add_after(
				'unlink',
				'イメージ削除',
				array('type' => 'checkbox', 'description' => '削除したいファイルにチェックを入れてください。', 'options' => $select),
				array(),
				'upload[]');
		}

		return $form;
	}

}


