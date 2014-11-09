<?php
namespace Help;
class Model_Help extends \Locomo\Model_Base
{
//	use \Workflow\Traits_Model_Workflow;

	protected static $_table_name = 'helps';
	public static $_subject_field_name = 'SOME_TRAITS_USE_SUBJECT_FIELD_NAME';

	protected static $_properties = array(
		'id',
		'title',
		'controller',
		'body',
		'updated_at',
		'deleted_at',
		'creator_id',
		'modifier_id',
		'order',
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
		'Orm\Observer_UpdatedAt' => array(
				'events' => array('before_save'),
				'mysql_timestamp' => true,
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

		//title - 表題
		$form->add(
			'title',
			'表題',
			array('type' => 'text', 'size' => 30, 'class' => 'varchar')
		)
		->add_rule('required')
		->add_rule('max_length', 255)
		->set_value(@$obj->title);

		//controller - コントローラ
		$controllers = array('all' => '共通ヘルプ');
		foreach(\Util::get_all_configs() as $controller => $config):
			if( ! $config['main_controller']) continue;
			$controllers[$config['main_controller']] = $config['nicename'];
		endforeach;
		$checked = isset($obj->controller) ? $obj->controller : '';
		$form->add(
			'controller',
			'コントローラ',
			array('type' => 'select', 'style' => 'width: 30%;', 'options' => $controllers, 'class' => 'varchar')
		)
		->add_rule('required')
		->add_rule('max_length', 255)
		->set_value($checked);

		//body - 本文
		$form->add(
			'body',
			'本文',
			array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;', 'class' => 'text')
		)
		->add_rule('required')
		->set_value(@$obj->body);

		//order - 
		$form->add(
			'order',
			'order',
			array('type' => 'text', 'size' => 5, 'class' => 'int[5]')
		)
		->add_rule('required')
		->add_rule('max_length', 5)
		->set_value(@$obj->order);



		static::$_cache_form_definition = $form;
		return $form;
	}
}
