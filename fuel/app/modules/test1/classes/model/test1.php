<?php
namespace Test1;
class Model_Test1 extends \Locomo\Model_Base
{
//	use \Workflow\Traits_Model_Workflow;

	protected static $_table_name = 'test1s';
	public static $_subject_field_name = 'SOME_TRAITS_USE_SUBJECT_FIELD_NAME';

	protected static $_properties = array(
		'id',
		'title',
		'body',
		'is_bool',
		'created_at',
		'updated_at',
		'expired_at',
		'deleted_at',
		'is_visible',
		'creator_id',
		'modifier_id',
	);

	protected static $_depend_modules = array();

	//$_option_options - see sample at \User\Model_Usergroup
	public static $_option_options = array();

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
		'Locomo\Observer_Expired' => array(
				'events' => array('before_insert', 'before_save'),
				'properties' => array('expired_at'),
			),
		'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
//		'Workflow\Observer_Workflow' => array(
//			'events' => array('before_insert', 'before_save','after_load'),
//		),
//		'Revision\Observer_Revision' => array(
//			'events' => array('after_insert', 'after_save', 'before_delete'),
//		),

	);

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory, $obj = null)
	{
		if(static::$_cache_form_definition && $obj == null) return static::$_cache_form_definition;

		//forge
		$form = \Fieldset::forge($factory, \Config::get('form'));
/*
		//user_name
		$val->add('name', 'サンプル')
			->add_rule('required')
			->add_rule('max_length', 50)
			->add_rule('valid_string', array('alpha','numeric','dot','dashes',))
			->add_rule('unique', "users.user_name.".@$obj->id);
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('max_length', 255)
			->add_rule('unique', "users.email.".@$obj->id);
*/
		//title - 表題
		$form->add(
			'title',
			'表題',
			array('type' => 'text', 'size' => 30, 'class' => 'varchar')
		)
		->add_rule('required')
		->add_rule('max_length', 255)
		->set_value(@$obj->title);

		//body - 本文
		$form->add(
			'body',
			'本文',
			array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;', 'class' => 'text')
		)
		->add_rule('required')
		->set_value(@$obj->body);

		//is_bool - 真偽値
		$form->add(
			'is_bool',
			'真偽値',
			array('type' => 'select', 'options' => array(0, 1), 'class' => 'bool')
		)
		->set_value(@$obj->is_bool);

		//created_at - 作成日時
		$form->add(
			'created_at',
			'作成日時',
			array('type' => 'text', 'size' => 20, 'placeholder' => date('Y-m-d H:i:s'), 'class' => 'datetime')
		)
		->set_value(isset($obj->created_at) ? $obj->created_at : date('Y-m-d H:i:s'));

		//expired_at - 有効期日
		$form->add(
			'expired_at',
			'有効期日',
			array('type' => 'text', 'size' => 20, 'placeholder' => date('Y-m-d H:i:s'), 'class' => 'datetime')
		)
		->set_value(@$obj->expired_at);

		//is_visible - 可視属性
		if(\Auth::get_user_id() >= 0):
			$form->add(
					'is_visible',
					'可視属性',
					array('type' => 'hidden', 'value' => 1)
				)
			->add_rule('required')

				->set_value(@$obj->is_visible);
		else:
			$form->add(
					'is_visible',
					'可視属性',
					array('type' => 'select', 'options' => array('0' => 'no', '1' => 'yes'), 'default' => 1, 'class' => 'int')
				)
			->add_rule('required')

				->set_value(@$obj->is_visible);
		endif;



		static::$_cache_form_definition = $form;
		return $form;
	}
}
