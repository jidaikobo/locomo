<?php
namespace Locomo;
class Model_Frmt_Table extends \Locomo\Model_Base_Soft
{

	public static $_format_model = '';
	public static $_format_model_relate = '';
	public static $_format_pdf_fields = array(
	);

	public static $_format_excel_fields = array(
	);

	public static function _init()
	{
		parent::_init();

		$_properties['model']['default'] = static::$_format_model;

		/*
		 * static::$_options にすると
		 * set_public_options や set_public_options の merge で消されてしまう。
		 * 絶対に where 句に入る検索条件は $_conditions に書く
		 */
		if (static::$_format_model) static::$_conditions['where'][] = array('model', '=', static::$_format_model);
	}

	protected static $_table_name = 'lcm_frmt_tables';

	// $_conditions
	protected static $_conditions = array(
		'order_by' => array('seq' => 'asc'),
	);
	public static $_options = array();

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'is_draft' => array(
			'label' => '下書き',
			'form' => array(
				'type' => 'radio',
				'options' => array(
					1 => '下書き',
					0 => '使用',
				),
				'template' => 'opener',
			),
			'default' => 1,
		),

		'seq' => array(
			'label' => '表示順',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'class' => 'ar',
				'template' => 'closer',
			),
			'default' => 0,
		),
		'name' => array(
			'label' => '名前',
			'form' => array(
				'type' => 'text',
				'size' => 45,
			),
			'default' => '',
			'validation' => array(
				'required',
			),
		),
		'relation' => array(
			'label' => 'リレーション',
			'form' => array(
				'type' => 'select',
			),
			'default' => '',
			'validation' => array(
				'required',
			),
		),
		'is_print_header' => array(
			'label' => 'ヘッダーを印刷する',
			'form' => array(
				'type' => 'radio',
				'options' => array(
					1 => 'ヘッダーあり',
					0 => 'なし',
				),
			),
			'default' => 1,
		),


		'header_min_h' => array(
			'label' => 'ヘッダー高さ(最小)',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'class' => 'ar',
			),
			'default' => 0,
		),
		'header_padding_left' => array(
			'label' => 'ヘッダーセル余白左',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_left ar',
				'template' => 'opener',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'header_padding_top' => array(
			'label' => 'ヘッダーセル余白上',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_top ar',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'header_padding_right' => array(
			'label' => 'ヘッダーセル余白右',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_right ar',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'header_padding_bottom' => array(
			'label' => 'ヘッダーセル余白下',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_bottom ar',
				'template' => 'closer',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'header_font_size' => array(
			'label' => 'ヘッダーフォントサイズ',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'font_size ar',
				'template' => 'opener',
			),
			'default' => 12,
			'unit' => 'pt',
		),
		'header_font_family' => array(
			'label' => 'ヘッダーフォントファミリー',
			'form' => array(
				'type' => 'select',
				'class' => 'font_family',
				'options' => array(
					'M' => '明朝体',
					'G' => 'ゴシック体',
				),
				'template' => 'closer',
			),
			'default' => 'M',
		),
		'header_align' => array(
			'label' => 'ヘッダー左右文字揃え',
			'form' => array(
				'type' => 'select',
				'options' => array(
					'L' => '左',
					'C' => '中央',
					'R' => '右',
				),
				'class' => 'align',
			),
			'default' => 'L',
		),

		'model' => array(
			'form' => array(
				'type' => false,
			),
			'default' => '',
		),

		'created_at' => array('form' => array('type' => false), 'default' => null),
		'updated_at' => array('form' => array('type' => false), 'default' => null),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
		'creator_id' => array('form' => array('type' => false), 'default' => ''),
		'updater_id' => array('form' => array('type' => false), 'default' => ''),
	);

	// $_observers
	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save'),
		),
	);

	public function _event_before_save()
	{
		$this->model = static::$_format_model;
	}

	protected static $_has_many = array(
		'element' => array(
			'key_from' => 'id',
			'model_to' => '\Locomo\Model_Frmt_Table_Element',
			'key_to' => 'frmt_table_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		),
	);

	/*
	 * set_search_options()
	 */
	public static function set_search_options()
	{

		static::set_equal_options(
			array(
				'id',
			)
		, \Input::get('searches'));
		static::set_like_options(
			array(
				'name',
			)
		, \Input::get('likes'));

		if (\Input::get('is_draft'))
		{
			if (\Input::get('is_draft') == 'use') static::$_options['where'][] = array('is_draft', false);
			if (\Input::get('is_draft') == 'draft') static::$_options['where'][] = array('is_draft', true);
		}
	}

	public static function format_import_matcher($origin, $new)
	{
		$match = true;
		foreach ($origin as $key => $value)
		{
			if ($origin[$key] != $new[$key])
			{
				$match = false;
				break;
			}
		}
		return $match;
	}


}

