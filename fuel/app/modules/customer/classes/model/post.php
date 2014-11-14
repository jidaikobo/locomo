<?php
namespace Customer;
class Model_Post extends \Model_Post {

	public static $_conditions = array(
		'where' => array(
			array('is_customer', true),
		),
	);
}

