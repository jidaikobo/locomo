<?php
namespace Locomo;
class Presenter_Impt_Index_Admin extends \Presenter_Base
{

	public static $_test_objects = null;

	public static function search_form($title)
	{
		$config = \Config::load('form_search','form_search',true,true);
		// 検索用form
		$form = \Fieldset::forge('frmt', $config);

		$form->add('searches[id]', 'ID', array(
			'type' => 'text',
			'size' => 6,
			'value' => \Input::get('searches.id'),
		))
			->set_template('opener');

		$form->add('likes[name]', 'フォーマット名', array(
			'type' => 'text',
			'value' => \Input::get('likes.name'),
		))
			->set_template('closer');

		$form->add('is_draft', '使用状態', array(
			'type' => 'select',
			'options' => array(
				'' => '',
				'use' => '使用中のみ',
				'draft' => '下書きのみ',
			),
			'value' => \Input::get('is_draft'),
		))
			->set_template('opener');

		/*
		$form->add('searches[is_multiple]', '1ページ内に複数印刷', array(
			'type' => 'select',
			'options' => array(
				'' => '',
				0  => '単数のみ',
				1  => '複数印刷のみ',
			),
			'value' => \Input::get('searches.is_draft'),
		));
		 */

		$form->add('searches[type]', 'タイプ', array(
			'type' => 'select',
			'options' => array(
				''      => '',
				'pdf'   => 'pdf のみ',
				'excel' => 'excel のみ',
			),
			'value' => \Input::get('searches.type'),
		))
			->set_template('closer');

		// wrapper
		$parent = parent::search_form($title ?: 'フォーマット一覧');
		$parent->add_after($form, 'counseling', array(), array(), 'opener');

		return $parent;
	}


	// @Override
	public static function create_import_url($obj)
	{
		// control を許す $obj->type
		$controllables = array(
			'pdf',
			'excel',
			'csv',
		);

		if (! in_array($obj->type, $controllables)) return '';

		$html = '';
		$crtl = \Inflector::ctrl_to_dir(\Request::main()->controller);
		$crtl_name = \Inflector::add_head_backslash(\Request::main()->controller);

		/* 当面閲覧なし
		if (\Auth::has_access($crtl_name.'/view'))
		{
			$html.= \Html::anchor($crtl.'/view/'.$obj->id, '閲覧', array('class' => 'view'));
		}
		 */

		if (\Auth::has_access($crtl_name.'/import'))
		{
			$html.= \Html::anchor($crtl.'/import/'.$obj->id, 'インポート', array('class' => 'edit button small'));
		}
		$html = $html ? '<div class="btn_group ac">'.$html.'</div>' : '' ;

		return $html;
	}
}
