<?php
namespace User;

class Model_User extends \Kontiki\Model_User_Abstract
{
	protected static $_has_one = array(
		//memo
		'memo' => array(
			'model_to' => '\Meta\Model_Meta',
			'key_from' => 'id',
			'key_to' => 'controller_id',
			'conditions' => array(
				'where' => array(
					array('controller', '=', 'user'),
					array('meta_key', '=', 'memo'),
				),
			),
		),
	) ;
	protected static $_has_many = array(
		//userown
		'userown' => array(
			'model_to' => '\Meta\Model_Meta',
			'key_from' => 'id',
			'key_to' => 'controller_id',
			'conditions' => array(
				'where' => array(
					array('controller', '=', 'user'),
					array('meta_key', '=', 'userown'),
				),
			),
		),
		//phones
		'phones' => array(
			'model_to' => '\Meta\Model_Meta',
			'key_from' => 'id',
			'key_to' => 'controller_id',
			'conditions' => array(
				'where' => array(
					array('controller', '=', 'user'),
					array('meta_key', '=', 'phones'),
				),
			),
		),
	) ;
}
