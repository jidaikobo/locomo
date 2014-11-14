<?php
namespace Customer;
class Model_Item extends \Model_Item {

	public static $_conditions = array(
		'where' => array(
			array('category', 'LIKE', 'ユーザー区分%'),
		),
	);
}
