<?php
namespace Locomo;
class Presenter_Frmt_Index_Admin extends \Presenter_Base
{

	public static $_test_objects = null;

	public static function search_form($title)
	{
		$config = \Config::load('form_search','form_search',true,true);
		// 検索用form
		$form = \Fieldset::forge('counseling', $config);

		$form->add('counseled_at', '日付で絞り込み', array(
			'type' => 'text',
			'value' => \Input::get('counseled_at'),
			'class' => 'date',
		));


		$form->add('assessment[id]', '利用者ID', array(
			'type' => 'text',
			'value' => \Input::get('assessment.id'),
		))
			->set_template('opener');

		$form->add('assessment[name]', '利用者名前', array(
			'type' => 'text',
			'value' => \Input::get('assessment.name'),
		));

		$form->add('assessment[area_type]', '利用者地域区分', array(
			'type' => 'select',
			'options' => array(
				'' => '',
				'市内' => '市内',
				'府内' => '府内',
				'他府県' => '他府県',
			),
			'value' => \Input::get('assessment.area_type'),
		))
			->set_template('closer');


		$form->add('likes[title]', '件名', array(
			'type' => 'text',
			'value' => \Input::get('likes.title'),
		))
			->set_template('opener');

		$form->add('likes[contents]', '相談内容', array(
			'type' => 'text',
			'value' => \Input::get('likes.contents'),
		))
			->set_template('closer');

		// wrapper
		$parent = parent::search_form($title ?: '相談支援');
		$parent->add_after($form, 'counseling', array(), array(), 'opener');

		return $parent;
	}


	// @Override
	public static function create_ctrls($obj)
	{
		// control を許す $obj->type
		$controllables = array(
			'pdf',
			'excel',
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

		if (\Auth::has_access($crtl_name.''.$obj->type.'_edit/'))
		{
			$html.= \Html::anchor($crtl.'/'.$obj->type.'_edit/'.$obj->id, '編集', array('class' => 'edit'));
		}
		if (\Auth::has_access($crtl_name.''.$obj->type.'_edit_element/'))
		{
			$html.= \Html::anchor($crtl.'/'.$obj->type.'_edit_element/'.$obj->id, '要素の編集', array('class' => 'edit'));
		}

		if (is_subclass_of($obj, '\Orm\Model_Soft'))
		{
			if (\Auth::has_access($crtl_name.'/delete'))
			{
				if ($obj['deleted_at'])
				{
					$html.= \Html::anchor($crtl.'/undelete/'.$obj->id, '復活', array('class' => 'undelete confirm'));
					if (\Auth::has_access($crtl_name.'/purge_confirm'))
					{
						$html.= \Html::anchor($crtl.'/purge_confirm/'.$obj->id, '完全に削除', array('class' => 'delete confirm'));
					}
				}
				else
				{
					$html.= \Html::anchor($crtl.'/delete/'.$obj->id, '削除', array('class' => 'delete confirm'));
				}
			}
		}
		else
		{
			if (\Auth::has_access($crtl_name.'/purge_confirm'))
			{
				$html.= \Html::anchor($crtl.'/purge_confirm/'.$obj->id, '完全に削除', array('class' => 'delete confirm'));
			}
		}
		$html = $html ? '<div class="btn_group">'.$html.'</div>' : '' ;

		return $html;
	}

	// Override用
	public static function create_preview($obj, $output_url)
	{
		if (! $output_url) return '';
		$model = $obj->model;
		if (! $model ) return '';


		$model::set_public_options();
		$model::set_search_options();
		$model::set_paginated_options();

		$model::$_options['from_cache'] = false;
		$model::$_options['rows_limit'] = 25;
		if (!static::$_test_objects)
		{
			static::$_test_objects = $model::find('all', $model::$_options);
		}

		$html = \Form::open(array('action' => $output_url, 'method' => 'post'));
		$html .= \Form::hidden('format', $obj->id);

		foreach (static::$_test_objects as $test_object)
		{
			$html .= \Form::hidden('ids['.$test_object->id.']', $test_object->id);
		}
		$html .= \Form::submit('test', 'テスト印刷', array('class' => 'btn small'));
		$html .= \Form::close();

		return $html;
	}

}
