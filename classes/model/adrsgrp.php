<?php
namespace Locomo;
class Model_Adrsgrp extends \Model_Base
{
//	use \Model_Traits_Wrkflw;

	protected static $_table_name = 'lcm_adrs_groups';

	public static $_options = array();

	// $_conditions
	protected static $_conditions = array(
		'order_by' => array('seq' => 'acs'),
	);

	protected static $_properties =
	array (
		'id',
		'name' => array(
			'label' => 'グループ名',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
			'validation' => 
			array (
				'unique' => array('lcm_adrs_groups.name'),
				'required',
				'max_length' => array (255,),
			),
		),
		'description' => array(
			'label' => '説明',
			'form' => array(
				'type' => 'text',
				'size' => 20,
			),
		),
		'seq' => array(
			'label' => '表示順',
			'form' => array(
				'type' => 'text',
				'size' => 5,
			),
		),
		'is_available' => array(
			'label' => '表示',
			'form' => array(
				'type' => 'select',
				'options' => array('0' => '未使用', '1' => '使用中')
			),
			'default' => 0
		),
	);

	protected static $_has_many = array(
		'address' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Adrs',
			'key_to' => 'group_id',
			'cascade_save' => false,
			'cascade_delete' => false
		)
	);

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
					array('description', 'LIKE', $all),
				) 
			);
		}
	}
}
