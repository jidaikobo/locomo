<?php
namespace Locomo;
class Presenter_Pg_Edit extends \Presenter_Base
{
	/**
	 * form()
	 * @return obj instanceof \Form
	 */
	public static function form($obj = null)
	{
		$form = parent::form($obj);

		$id = isset($obj->id) ? $obj->id : '';

		// pathとlangのセットで一意に
		$form->field('path')
			->add_rule(
				array(
					'pg_unique' =>
					function ($path) use ($obj)
					{
						if (isset($obj) && is_object($obj) )
						{
							// update
							if ($obj->id)
							{
								$r = \Model_Pg::find('all', array(
									'where' => array(
										array('id', '<>', $obj->id),
										array('path', $path),
										array('lang', $obj->lang),
									)
								));
								if (count($r) !== 0)
								{
									Validation::active()->set_message('pg_unique', 'ファイル名と言語は、サイト内で重複しないものを入力してください。');
									return false;
								}
							}
							// create
							else
							{
								$r = \Model_Pg::find('all', array(
									'where' => array(
										array('path', $path),
										array('lang', $obj->lang),
									)
								));
								if (count($r) !== 0)
								{
									Validation::active()->set_message('pg_unique', 'ファイル名と言語は、サイト内で重複しないものを入力してください。');
									return false;
								}
							}
						}
					}
				)
			);

		// 言語
		\Lang::load('nations');
		$options = array();
		foreach (array_map('basename', glob(LOCOMOPATH.'lang/*')) as $lang)
		{
			$options[$lang] = __($lang);
		}
		$form->field('lang')
			->set_value($obj->lang ?: \Lang::get_lang())
			->set_options($options);


		// カテゴリ
		$options = \Model_Pggrp::find_options('name', array('where' => array(array('is_available', true))));
		if ($options)
		{
			$form->add_after(
				'pggrp',
				'カテゴリ',
				array('type' => 'checkbox', 'options' => $options),
				array(),
				'url')
				->set_value(array_keys($obj->pggrp));
		}
		/*
		// modify template source
		$field_template = \Config::get('form')['field_template'];

		// add field
		$options = \Model_Name::find_options('name', array('where' => array(array('category', 'NAME'))));
		$form->add_after(
			'objname',
			'NAME',
			array('type' => 'checkbox', 'options' => $options),
			array(),
			'user_type')
			->set_value(array_keys($obj->objname));

		// set_tabular_form
		$tabular_form = \Fieldset::forge('relation_name')->set_tabular_form('Model_Name', 'relation_name', $obj, 2);
		$form->add_after($tabular_form, 'name', array(), array(), 'field_name');
*/

		return $form;
	}
}
