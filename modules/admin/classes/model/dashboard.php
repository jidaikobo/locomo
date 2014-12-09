<?php
namespace Admin;
class Model_Dashboard extends \Locomo\Model_Base {

	protected static $_table_name = 'admin_dashboards';

	protected static $_properties = array(
		'id',
		'user_id' => array(
			'label' => 'ユーザID',
		),
		'actions' => array(
			'label' => 'アクション',
		),
	);

	public static $_conditions = array(
		// 'order_by' => array('order'),
	);
}

