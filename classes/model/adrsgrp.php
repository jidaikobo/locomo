<?php
namespace Locomo;
class Model_Adrsgrp extends \Model_Base
{
//	use \Model_Traits_Wrkflw;

	protected static $_table_name = 'lcm_adrs_groups';

	public static $_options = array();

	// $_conditions
	protected static $_conditions = array(
		'where' => array(
			array('is_available', true)
		),
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
				'required',
				'max_length' => 
				array (
					255,
				),
			),
		),
		'description',
		'seq',
		'is_available',
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

}
