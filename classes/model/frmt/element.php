<?php
namespace Locomo;
class Model_Frmt_Element extends \Locomo\Model_Base_Soft
{
	protected static $_table_name = 'lcm_frmt_elements';

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
		'format_id' => array('form' => array('type' => false)),
		'txt' => array(
			'label' => 'テキスト',
			'form' => array(
				'type' => 'textarea',
				'class' => 'txt',
			),
			'default' => '',
		),

		'name' => array(
			'label' => '名前',
			'form' => array(
				'type' => 'text',
				'size' => 20,
				'class' => 'name',
				'placeholder' => '新規要素',
			),
			'default' => '', // 空にしておけば js 側で勝手に入る
		),
		'seq' => array(
			'label' => '印刷順',
			'form' => array(
				'type' => 'hidden',
				'size' => 10,
				'class' => '',
			),
			'default' => 0,
		),
		'x' => array(
			'label' => 'X',
			'form' => array(
				'type' => 'text',
				'size' => 5,
				'class' => 'x',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'y' => array(
			'label' => 'Y',
			'form' => array(
				'type' => 'text',
				'size' => 5,
				'class' => 'y',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'ln_y' => array(
			'label' => 'Y相対',
			'form' => array(
				'type' => 'checkbox',
				'options' => array(
					1 => '上位要素の下に配置',
				),
				'class' => 'ln_y',
			),
			'default' => 0,
		),
		'w' => array(
			'label' => '幅',
			'form' => array(
				'type' => 'text',
				'size' => 5,
				'class' => 'w',
			),
			'default' => 50,
			'unit' => 'mm',
		),
		'h' => array(
			'label' => '高さ',
			'form' => array(
				'type' => 'text',
				'size' => 5,
				'class' => 'h',
			),
			'default' => 25,
			'unit' => 'mm',
		),
		'h_adjustable' => array(
			'label' => '可変高',
			'form' => array(
				'type' => 'checkbox',
				'options' => array(
					1 => '高さはテキスト量に依存する',
				),
				'class' => 'h_adjustable',
			),
			'default' => 0,
		),
		'padding_left' => array(
			'label' => 'セル余白左',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_left',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'padding_top' => array(
			'label' => 'セル余白上',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_top',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'padding_right' => array(
			'label' => 'セル余白右',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_right',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'padding_bottom' => array(
			'label' => 'セル余白下',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_bottom',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		/*
		'margin_left' => array(
			'label' => '余白左',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'margin_left',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		 */
		'margin_top' => array(
			'label' => '余白上',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'margin_top',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'font_size' => array(
			'label' => 'フォントサイズ',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'font_size',
			),
			'default' => 12,
			'unit' => 'pt',
		),
		'font_family' => array(
			'label' => 'フォントファミリー',
			'form' => array(
				'type' => 'select',
				'class' => 'font_family',
				'options' => array(
					'M' => '明朝体',
					'G' => 'ゴシック体',
				),
			),
			'default' => 'M',
		),
		'align' => array(
			'label' => '左右文字揃え',
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
		'valign' => array(
			'label' => '上下文字揃え',
			'form' => array(
				'type' => 'select',
				'options' => array(
					'T' => '上',
					'M' => '中央',
					'B' => '下',
				),
				'class' => 'valign',
			),
			'default' => 'T',
		),
		/*
		'border_width' => array(
			'label' => '罫線幅',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'border_width',
			),
			'default' => 1,
			'unit' => 'pt',
		),
		 */
		'border_left' => array(
			'label' => '罫線左',
			'form' => array(
				'type' => 'checkbox',
				'options' => array(
					1 => '罫線左',
				),
				'class' => 'border_left',
			),
			'default' => 0,
		),
		'border_top' => array(
			'label' => '罫線上',
			'form' => array(
				'type' => 'checkbox',
				'options' => array(
					1 => '罫線上',
				),
				'class' => 'border_top',
			),
			'default' => 0,
		),
		'border_right' => array(
			'label' => '罫線右',
			'form' => array(
				'type' => 'checkbox',
				'options' => array(
					1 => '罫線右',
				),
				'class' => 'border_right',
			),
			'default' => 0,
		),
		'border_bottom' => array(
			'label' => '罫線下',
			'form' => array(
				'type' => 'checkbox',
				'options' => array(
					1 => '罫線下',
				),
				'class' => 'border_bottom',
			),
			'default' => 0,
		),

		'type' => array('form' => array('type' => 'text', 'class' => 'type'), 'default' => ''),

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


	public static $format_options = array(
	);

	public static $model_properties = array(
	);


}
