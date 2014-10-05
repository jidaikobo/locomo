<?php
namespace Test;
class Model_Test extends \Kontiki\Model_Crud
{
	protected static $_table_name = 'tests';
	protected static $_primary_name = '';

	protected static $_properties = array(
		'id',
		'title' => array(
			'label' => '表題',
		),
		'body' => array(
			'label' => '本文',
		),
		'is_bool' => array(
			'label' => '真偽値',
		),
		'status' => array(
			'label' => '状態',
		),
		'created_at' => array(
			'label' => '',
		),
		'expired_at' => array(
			'label' => '',
		),
		'deleted_at' => array(
			'label' => '',
		),

// 'workflow_status',
	);

/*
	//observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);
	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
		'Kontiki\Observer\Date' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
	);
*/

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory, $id = '')
	{
		$form = \Fieldset::forge('form', \Config::get('form'));

		//user_name
		$form->add(
				'title',
				'ユーザ名',
				array('type' => 'textarea', 'cols' => 70, 'rows' => 2)
			)
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',))
			->add_rule('unique', "tests.title.{$id}");

		//user_name
		$form->add(
				'body',
				'ほんぶん',
				array('type' => 'textarea', 'cols' => 70, 'rows' => 3)
			)
			->add_rule('required');

		return $form;
	}
}
