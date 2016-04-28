<?php
namespace Locomo;
class Model_Frmt extends \Locomo\Model_Base_Soft
{
	public static $_format_model = '';


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

	protected static $_table_name = 'lcm_frmts';

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
				'class' => '',
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
		'w' => array(
			'label' => '用紙幅',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'opener',
			),
			'unit' => 'mm',
			'default' => 210, // A4 縦
		),
		'h' => array(
			'label' => '用紙高さ',
			'form' => array(
				'type' => 'text',
				'size' => 3,
			),
			'unit' => 'mm',
			'default' => 297, // A4 縦
		),
		'rotation' => array(
			'label' => '用紙回転',
			'form' => array(
				'type' => 'select',
				'options' => array(
					0 => 'なし',
					90 => '右',
					270 => '左',
					180 => '上下逆',
				),
				'template' => 'closer',
			),
			'default' => '',
			'validation' => array(
				'required',
			),
		),

		// 以下 タックシールなどタックシールなど複数印刷用プロパティ
		'is_multiple' => array(
			'label' => '1ページ内に複数印刷',
			'form' => array(
				'type' => 'radio',
				'options' => array(
					1 => '複数印刷',
					0 => '単数',
				),
			),
			'default' => 0,
		),

		'margin_top' => array(
			'label' => '余白上',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'opener',
				'class' => 'for_multiple',
			),
			'unit' => 'mm',
			'default' => 0.0,
		),
		'margin_bottom' => array(
			'label' => '余白下',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'class' => 'for_multiple',
			),
			'unit' => 'mm',
			'default' => 0.0,
		),
		'margin_left' => array(
			'label' => '余白左',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'class' => 'for_multiple',
			),
			'unit' => 'mm',
			'default' => 0.0,
		),
		'margin_right' => array(
			'label' => '余白右',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'closer',
				'class' => 'for_multiple',
			),
			'unit' => 'mm',
			'default' => 0.0,
		),

		'cols' => array(
			'label' => '列',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'opener',
				'class' => 'for_multiple',
			),
			'default' => 2,
		),
		'rows' => array(
			'label' => '行',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'closer',
				'class' => 'for_multiple',
			),
			'default' => 5,
		),

		'cell_w' => array(
			'label' => 'セル幅',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'opener',
				'class' => 'for_multiple',
			),
			'unit' => 'mm',
			'default' => 0,
		),
		'cell_h' => array(
			'label' => 'セル高さ',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'closer',
				'class' => 'for_multiple',
			),
			'unit' => 'mm',
			'default' => 0,
		),
		'space_horizontal' => array(
			'label' => 'セルの間隔 左右',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'opener',
				'class' => 'for_multiple',
			),
			'unit' => 'mm',
			'default' => 0.0,
		),
		'space_vertical' => array(
			'label' => 'セルの間隔 上下',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'class' => 'for_multiple',
				'template' => 'closer',
			),
			'unit' => 'mm',
			'default' => 0.0,
		),

		'type' => array(
			'form' => array(
				'type' => false,
			),
			'default' => 'pdf',
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
			'model_to' => '\Locomo\Model_Frmt_Element',
			'key_to' => 'format_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		),
		// EAV
		'eav' => array(
			'key_from' => 'id',
			'model_to' => '\Locomo\Model_Frmt_Eav',
			'key_to' => 'format_id',
			'cascade_save' => true,
			'cascade_delete' => true,
		),
	);

	protected static $_eav = array(
		'eav' => array(
			'attribute' => 'key',
			'value' => 'value',
		)
	);

	/*
	 * set_search_options()
	 */
	public static function set_search_options()
	{

		static::set_equal_options(
			array(
				'id',
				'is_multiple',
				'type',
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
