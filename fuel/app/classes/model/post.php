<?php
class Model_Post extends \Locomo\Model_Base {

	protected static $_table_name = 'posts';

	protected static $_properties = array(
		'id',
		'name',
		'is_customer',
		'bk_id',
		'gid_bk_merged',
		'sys_name',
		'director_position',
		'director_name',
		'zip',
		'address',
		'tel',
		'fax',
		'mail',
		'url',
		'office_no',
		'office_details_no',
		'office_name',
		'office_kana',
		'employer_name',
		'employer_kana',
		'dept_position',
		'dept_position_kana',
		'dept_name',
		'depo_kana',
		'jigyo_type',
		'facility_type',
		'area_type',
		'reduction_type',
		'reduction_max_price',
		'banking_account_number',
		'memo',
		'is_status',
		'allowance',
	);

	public static $_conditions = array(
		// 'order_by' => array('order'),
	);
}

