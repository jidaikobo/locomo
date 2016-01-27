<?php
namespace Locomo;
class Model_Bkmk extends \Locomo\Model_Base
{
	protected static $_table_name = 'lcm_bkmks';

	// $_conditions
	protected static $_conditions = array(
		'order_by' => array('seq' => 'asc'),
	);
	public static $_options = array();

	/**
	 * $_properties
	 */
	protected static $_properties = array(
		'id',
		'user_id' => array(
			'label' => 'ユーザーID',
			'form' => array('type' => false),
		),
		'name' => array(
			'label' => '名前',
			'form' => array(
				'type' => 'text',
				'size' => 45,
			),
			'default' => '',
			'validation' => array(
				'required',
			),
		),
		'seq' => array(
			'label' => '表示順',
			'form' => array(
				'type' => 'text',
				'size' => 3,
				'class' => '',
			),
			'default' => 0,
		),

		'url' => array(
			'label' => 'URL',
			'form' => array(
				'type' => 'text',
				'size' => 50,
			),
			'default' => '',
		),
	);

	// $_observers
	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
	);

	public function _event_before_save()
	{
		$this->user_id = \Auth::get('id');
	}

	/* リレーションはっちゃ駄目
	protected static $_has_many = array(
		'user' => array(
			'key_from' => 'user_id',
			'model_to' => '\Locomo\Model_Usr',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);
	 */
}
