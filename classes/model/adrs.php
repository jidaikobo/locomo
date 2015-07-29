<?php
namespace Locomo;
class Model_Adrs extends \Model_Base_Soft
{
	protected static $_table_name = 'lcm_adrs';

	protected static $_properties =
	array (
		'id',
		'group_id' => array (
			'label' => 'グループ',
			'default' => ''
		),
		'name' => array(
			'label' => '氏名',
			'lcm_role' => 'subject',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 60,
				'class' => 'varchar',
			),
			'validation' => 
			array (
				'required',
				'max_length' => 
				array (
					255,
				),
			),
		),
		'kana' => 
		array (
			'label' => 'かな',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 60,
				'class' => 'varchar',
			),
			'validation' => 
			array (
				'required',
				'max_length' => 
				array (
					255,
				),
			),
		),
		'company_name' => 
		array (
			'label' => '会社名',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 60,
				'class' => 'varchar',
			),
			'default' => ''
		),
		'company_kana' => 
		array (
			'label' => '会社名かな',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 60,
				'class' => 'varchar',
			),
			'default' => ''
		),
		'tel' => 
		array (
			'label' => '電話番号',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 20,
				'class' => 'varchar',
			),
			'default' => ''
		),
		'fax' => 
		array (
			'label' => 'FAX番号',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 20,
				'class' => 'varchar',
			),
			'default' => ''
		),
		'mail' => 
		array (
			'label' => 'メールアドレス',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 40,
				'class' => 'varchar',
			),
			'default' => ''
		),
		'mobile' => 
		array (
			'label' => '携帯電話',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 20,
				'class' => 'varchar',
			),
			'default' => ''
		),
		'zip3' => array(
			'label' => '郵便番号',
			'data_type' => 'varchar(3)',
			'form' => array(
				'type' => 'text',
				'size' => 3,
			),
			'validation' => array(
				'max_length' => array(3),
			),
			'default' => '',
		),
		'zip4' => array(
			'label' => '郵便番号(下4桁)',
			'data_type' => 'varchar(4)',
			'form' => array(
				'type' => 'text',
				'size' => 4,
			),
			'validation' => array(
				// 'required',
				'max_length' => array(4),
			),
			'default' => '',
		),
		'address' => 
		array (
			'label' => '住所',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 60,
				'class' => 'varchar',
			),
			'default' => ''
		),
		'memo' => 
		array (
			'label' => '備考',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'textarea',
				'rows' => 7,
				'style' => 'width:100%;',
				'class' => 'text',
			),
			'default' => ''
		),
		'created_at' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		),
		'updated_at' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		),
		'deleted_at' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		),
		'creator_id' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		),
		'updater_id' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		),
	) ;

	//$_conditions
	protected static $_conditions = array();
	public static $_options = array();

	protected static $_belongs_to = array(
		'group' => array(
			'key_from' => 'group_id',
			'model_to' => 'Model_Adrsgrp',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);

	//_soft_delete
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	//observers
	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
		'Orm\Observer_UpdatedAt' => array(
				'events' => array('before_update'),
				'mysql_timestamp' => true,
			),
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
			'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Locomo\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save', 'before_delete'),
		),
//		'Locomo\Observer_Workflow' => array(
//			'events' => array('before_insert', 'before_save','after_load'),
//		),
	);

	// _event_before_save
	public function _event_before_save()
	{
		// 郵便番号 数字のみに
		$this->zip3 = mb_ereg_replace('[^0-9]','' , mb_convert_kana($this->zip3, 'a'));
		$this->zip4 = mb_ereg_replace('[^0-9]','' , mb_convert_kana($this->zip4, 'a'));
	}

	/**
	 * set_search_options()
	 */
	public static function set_search_options()
	{
		// free word search
		$all = \Input::get('all') ? '%'.\Input::get('all').'%' : '' ;
		if ($all)
		{
			static::$_options['where'][] = array(
				array('name', 'LIKE', $all),
				'or' => array(
					array('kana', 'LIKE', $all),
					'or' => array(
						array('company_name', 'LIKE', $all), 
						'or' => array(
							array('company_kana', 'LIKE', $all),
							'or' => array(
								array('mail', 'LIKE', $all),
								'or' => array(
									array('address', 'LIKE', $all),
									'or' => array(
										array('memo', 'LIKE', $all),
									)
								)
							)
						)
					)
				) 
			);
		}

		// group
		$group = \Input::get('group', null) ;
		if ($group)
		{
			static::$_options['where'][] = array(
				array('group_id', '=', $group),
			);
		}
	}
}
