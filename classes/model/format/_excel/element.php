<?php
namespace Locomo;
class Model_Format_Excel_Element extends \Locomo\Model_Base
{
	protected static $_table_name = 'lcm_format_elements';

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
	);
}

