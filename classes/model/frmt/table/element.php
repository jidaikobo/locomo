<?php
namespace Locomo;
class Model_Frmt_Table_Element extends \Locomo\Model_Base_Soft
{
	protected static $_table_name = 'lcm_frmt_table_elements';

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
		'frmt_table_id' => array('form' => array('type' => false)),
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
		'label' => array(
			'label' => 'ラベル',
			'form' => array(
				'type' => 'text',
				'size' => 20,
				'class' => 'label',
				'placeholder' => 'ヘッダーラベル',
			),
			'default' => '',
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
		'fitcell_type' => array(
			'label' => '文字のはみ出し',
			'form' => array(
				'type' => 'select',
				'options' => array(
					'hidden'   => '表示しない',
					'sizedown' => '文字を縮小しておさめる',
					'expand'   => 'セルを拡張する',
				),
				'class' => 'fitcell_type',
			),
			'default' => 'expand',
		),

		'min_h' => array(
			'label' => '高さ(最小)',
			'form' => array(
				'type' => 'text',
				'size' => 5,
				'class' => 'min_h ar',
			),
			'default' => 25,
			'unit' => 'mm',
		),
		'padding_left' => array(
			'label' => 'セル余白左',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_left ar',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'padding_top' => array(
			'label' => 'セル余白上',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_top ar',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'padding_right' => array(
			'label' => 'セル余白右',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_right ar',
			),
			'default' => 0,
			'unit' => 'mm',
		),
		'padding_bottom' => array(
			'label' => 'セル余白下',
			'form' => array(
				'type' => 'text',
				'size' => 10,
				'class' => 'padding_bottom ar',
			),
			'default' => 0,
			'unit' => 'mm',
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


	public static $format_options = array(
	);

	public static $model_properties = array(
	);


}

