<?php
namespace Meta;

class Model_Meta extends \Locomo\Model_Meta_Abstract
{
	protected static $_observers = array(
	);

	protected static $_belongs_to = array(
		'userown' => array(
			'model_to' => '\User\Model_User',
			'key_from' => 'controller_id',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);
}
