<?php
namespace Locomo;
class Model_Format_Excel_Format extends \Locomo\Model_Base
{
	protected static $_table_name = 'lcm_formats';

	// $_conditions
	protected static $_conditions = array(
		'where' => array(
			array('type' => 'excel'),
		),
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
		'type' => array(
			'form' => array(
				'type' => false,
			),
			'default' => 'excel',
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

	/**
	 * relations
	 */
	protected static $_has_many = array(
		'element' => array(
			'key_from' => 'id',
			'model_to' => '\Locomo\Model_Format_Excel_Element',
			'key_to' => 'format_id',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		// EAV
		'eav' => array(
			'key_from' => 'id',
			'model_to' => '\Locomo\Model_Format_Excel_Eav',
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
