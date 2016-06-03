<?php
namespace Locomo;
class Presenter_Frmt_Table_Index extends \Presenter_Base
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
		));


		// wrapper
		$parent = parent::search_form($title ?: 'フォーマット一覧');
		$parent->add_after($form, 'counseling', array(), array(), 'opener');

		return $parent;
	}


	// @Override
	public static function create_ctrls($obj)
	{
		$html = '';
		$crtl = \Inflector::ctrl_to_dir(\Request::main()->controller);
		$crtl_name = \Inflector::add_head_backslash(\Request::main()->controller);

		/* 当面閲覧なし
		if (\Auth::has_access($crtl_name.'/view'))
		{
			$html.= \Html::anchor($crtl.'/view/'.$obj->id, '閲覧', array('class' => 'view'));
		}
		 */
		if (\Auth::has_access($crtl_name.'/table_edit'))
		{
			$html.= \Html::anchor($crtl.'/table_edit/'.$obj->id, '編集', array('class' => 'edit button small'));
		}
		if (\Auth::has_access($crtl_name.'/table_edit_element'))
		{
			$html.= \Html::anchor($crtl.'/table_edit_element/'.$obj->id, '要素の編集', array('class' => 'edit button small'));
		}
		if (is_subclass_of($obj, '\Orm\Model_Soft'))
		{
			if (\Auth::has_access($crtl_name.'/delete'))
			{
				if ($obj['deleted_at'])
				{
					$html.= \Html::anchor($crtl.'/table_undelete/'.$obj->id, '復活', array('class' => 'undelete confirm button small'));
					if (\Auth::has_access($crtl_name.'/table_purge_confirm'))
					{
						$html.= \Html::anchor($crtl.'/table_purge_confirm/'.$obj->id, '完全に削除', array('class' => 'delete confirm button small'));
					}
				}
				else
				{
					$html.= \Html::anchor($crtl.'/table_delete/'.$obj->id, '削除', array('class' => 'delete confirm button small'));
				}
			}
		}
		else
		{
			if (\Auth::has_access($crtl_name.'/table_purge_confirm'))
			{
				$html.= \Html::anchor($crtl.'/table_purge_confirm/'.$obj->id, '完全に削除', array('class' => 'delete confirm button small'));
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
