<?php
namespace Test;
class Model_Test extends \Kontiki\Model_Crud
{
	protected static $_table_name = 'tests';
	protected static $_primary_name = '';

	protected static $_properties = array(
		'id',
		'title',
		'body',
		'is_bool',
		'status',
		'created_at',
		'expired_at',
		'deleted_at',

// 'workflow_status',
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

	/*
	 * __construct
	*/
	public function __construct(array $data = array(), $new = true, $view = null, $cache = true)
	{
		parent::__construct($data, $new, $view, $cache);
		foreach (self::$_depend_modules as $module) {
			\Module::load($module);
		}
	}

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

		//is_bool
		$form->add(
			'is_bool',
			'真偽値',
			array('type' => 'checkbox', 'options' => array(0, 1))
		)
		->set_value(@$obj->is_bool);

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



		return $form;
	}
}
