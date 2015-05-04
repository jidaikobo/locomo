<?php
namespace Locomo;
class Model_Hlp extends \Model_Base
{
	protected static $_table_name = 'lcm_hlps';

	protected static $_properties = array(
		'id',
		'title' => array(
			'lcm_role' => 'subject',
			'label' => '表題',
			'form' => array(
				'type' => 'hidden',
			),
			'validation' => array(
				'required',
				'max_length' => array(255),
			),
		),
		'ctrl' =>array(
			'label' => 'コントローラ',
			'form' => array(
				'type' => 'select',
				'style' => 'width: 30%;',
				'options' => array(),
				'class' => 'varchar',
			),
			'validation' => array(
				'required',
				'max_length' => array(255),
			),
		),
		'body' => array(
			'label' => '本文',
			'form' => array(
				'type' => 'textarea',
				'style' => 'width: 100%;',
				'rows' => '7',
				'class' => 'text tinymce',
			),
			'validation' => array(
//				'required',
			),
		),

		'creator_id' => array('form' => array('type' => false), 'default' => ''),
		'updater_id' => array('form' => array('type' => false), 'default' => ''),
		'created_at' => array('form' => array('type' => false), 'default' => null),
		'updated_at' => array('form' => array('type' => false), 'default' => null),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
	);

	//observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	protected static $_observers = array(
		'Orm\Observer_UpdatedAt' => array(
				'events' => array('before_update'),
				'mysql_timestamp' => true,
			),
		'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Locomo\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save', 'before_delete'),
		),
	);
}
