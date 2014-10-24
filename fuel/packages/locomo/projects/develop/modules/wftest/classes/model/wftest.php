<?php
namespace Wftest;
class Model_Wftest extends \Locomo_Core\Model_Base
{
	protected static $_table_name = 'wftests';
	protected static $_primary_name = '';

	protected static $_properties = array(
		'id',
		'title',
		'body',
		'status',
		'created_at',
		'updated_at',
		'expired_at',
		'deleted_at',
		'workflow_status',
	);

	protected static $_depend_modules = array(
	);

/*
	protected static $_has_many = array(
		'foo' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Foo',
			'key_to' => 'bar_id',
			'cascade_save' => true,
			'cascade_delete' => false
		)
	);
	protected static $_belongs_to = array(
		'foo' => array(
						'key_from' => 'foo_id',
						'model_to' => 'Model_Foo',
						'key_to' => 'id',
						'cascade_save' => true,
						'cascade_delete' => false,
					)
	);
*/

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
		'Locomo\Observer\Expired' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('expired_at'),
		),
		'Locomo\Observer\Workflow' => array(
			'events' => array('before_insert', 'before_save'),
			'properties' => array('workflow_status'),
		),
	);

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory, $obj = null, $id = '')
	{
		$form = \Fieldset::forge('form', \Config::get('form'));
/*
		//user_name
		$val->add('name', 'サンプル')
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',))
			->add_rule('unique', "users.user_name.{$id}");
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('max_length', 255)
			->add_rule('unique', "users.email.{$id}");
*/
		//title
		$form->add(
			'title',
			'表題',
			array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;')
		)
		->add_rule('required')
		->add_rule('max_length', 50)
		->set_value(@$obj->title);

		//body
		$form->add(
			'body',
			'本文',
			array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;')
		)
		->set_value(@$obj->body);

		//status
		$form->add(
			'status',
			'状態',
			array('type' => 'hidden')
		)
		->add_rule('max_length', 20)
		->set_value(@$obj->status);

		//created_at
		$form->add(
			'created_at',
			'created_at',
			array('type' => 'text', 'size' => 20, 'class' => 'calendar', 'placeholder' => date('Y-m-d H:i:s'))
		)
		->set_value(isset($obj->created_at) ? $obj->created_at : date('Y-m-d H:i:s'));

		//updated_at
		$form->add(
			'updated_at',
			'updated_at',
			array('type' => 'text', 'size' => 20, 'class' => 'calendar', 'placeholder' => date('Y-m-d H:i:s'))
		)
		->set_value(@$obj->updated_at);

		//expired_at
		$form->add(
			'expired_at',
			'expired_at',
			array('type' => 'text', 'size' => 20, 'class' => 'calendar', 'placeholder' => date('Y-m-d H:i:s'))
		)
		->set_value(@$obj->expired_at);

		//deleted_at
		$form->add(
			'deleted_at',
			'deleted_at',
			array('type' => 'text', 'size' => 20, 'class' => 'calendar', 'placeholder' => date('Y-m-d H:i:s'))
		)
		->set_value(@$obj->deleted_at);

		//workflow_status
		$form->add(
			'workflow_status',
			'workflow_status',
			array('type' => 'text', 'size' => 30)
		)
		->set_value(@$obj->workflow_status);

		return $form;
	}
}
