<?php
namespace Locomo;
class Model_Dashboard extends Model_Base
{
	protected static $_table_name = 'lcm_dashboards';

	protected static $_conditions = array(
		'order_by' => array(array('seq', 'ASC')),
	);
	public static $_options = array();


	public static $_properties = array(
		'id',
		'user_id' => array(
			'label' => 'ユーザID',
			'form' => array(
				'type' => false
			)
		),
		'action' => array(
			'label' => 'アクション',
			'form' => array(
				'type' => 'select',
				'options' => array(),// defined at \Admin\Model_User::form_definition()
			),
		),
		'size' => array(
			'label' => 'サイズ',
			'form' => array(
				'type' => 'select',
				'options' => array(''=>'サイズ', '3'=>'大', '2'=>'中', '1'=>'小'),
			),
		),
		'seq' => array(
			'label' => '順序',
			'form' => array(
				'type' => 'text',
				'attribute' => array('size' => '3'),
			),
		),
	);

}

