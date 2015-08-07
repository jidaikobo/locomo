<?php
namespace Locomo;
class Model_Pdf_Format extends \Locomo\Model_Base
{
	protected static $_table_name = 'lcm_pdf_formats';

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

		'is_visible' => array(
			'label' => '表示',
			'form' => array(
				'type' => 'radio',
				'options' => array(
					1 => 'する',
					0 => 'しない',
				),
			),
			'default' => 0,
		),
		'name' => array(
			'label' => '名前',
			'form' => array(
				'type' => 'text',
				'size' => 45,
				'template' => 'opener',
			),
			'default' => '',
			'validation' => array(
				'required',
			),
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
		'w' => array(
			'label' => '用紙幅',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'opener',
			),
			'default' => 210, // A4 縦
		),
		'h' => array(
			'label' => '用紙高さ',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'closer',
			),
			'default' => 297, // A4 縦
		),
		'margin_top' => array(
			'label' => '余白上',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'opener',
			),
			'default' => 0.0,
		),
		'margin_left' => array(
			'label' => '余白左',
			'form' => array(
				'type' => 'text',
				'size' => 3,
			),
			'default' => 0.0,
		),
		'margin_right' => array(
			'label' => '余白右',
			'form' => array(
				'type' => 'text',
				'size' => 3,
			),
			'default' => 0.0,
		),
		'margin_bottom' => array(
			'label' => '余白下',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'closer',
			),
			'default' => 0.0,
		),

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
		'cell_w' => array(
			'label' => 'セル幅',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'opener',
			),
			'default' => 0,
		),
		'cell_h' => array(
			'label' => 'セル高さ',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'closer',
			),
			'default' => 0,
		),
		'space_horizontal' => array(
			'label' => 'セルの間隔 左右',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'opener',
			),
			'default' => 0.0,
		),
		'space_vertical' => array(
			'label' => 'セルの間隔 上下',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'template' => 'closer',
			),
			'default' => 0.0,
		),
	);

	/**
	 * relations
	 */
	protected static $_has_many = array(
		'element' => array(
			'key_from' => 'id',
			'model_to' => '\Locomo\Model_Pdf_Element',
			'key_to' => 'format_id',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		// EAV
		'eav' => array(
			'key_from' => 'id',
			'model_to' => '\Locomo\Model_Pdf_Eav',
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
}
