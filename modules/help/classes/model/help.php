<?php
namespace Help;
class Model_Help extends \Locomo\Model_Base
{
//	use \Workflow\Traits_Model_Workflow;

	protected static $_table_name = 'helps';
	public static $_subject_field_name = 'title';

	protected static $_properties = array(
		'id',
		'title' => array(
			'label' => '表題',
			'form' => array(
				'type' => 'hidden',
			),
			'validation' => array(
				'required',
				'max_length' => array(255),
			),
		),
		'ctrl' =>array(
			'label' => 'コントローラ',
			'form' => array(
				'type' => 'select',
				'style' => 'width: 30%;',
				'options' => array(),
				'class' => 'varchar',
			),
			'validation' => array(
				'required',
				'max_length' => array(255),
			),
		),
		'body' => array(
			'label' => '本文',
			'form' => array(
				'type' => 'textarea',
				'style' => 'width: 30%;',
				'rows' => '7',
				'class' => 'text tinymce',
			),
			'validation' => array(
				'required',
			),
		),

		'creator_id' => array('form' => array('type' => false), 'default' => -1),
		'updater_id' => array('form' => array('type' => false), 'default' => -1),
		'created_at' => array('form' => array('type' => false), 'default' => null),
		'updated_at' => array('form' => array('type' => false), 'default' => null),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
	);

	protected static $_depend_modules = array();

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
		'Revision\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save', 'before_delete'),
		),
	);

	/**
	 * override properties()
	 * @return  array
	 */
	public static function properties()
	{
		$_properties = parent::properties();

		// ctrl
		$ctrl = urlencode(\Input::get('ctrl'));
		$actions = array('all' => '共通ヘルプ');
		$exceptions = array('\\Help\\Controller_Help', '\\Admin\\Controller_Admin', '\\Content\\Controller_Content');
		$controllers = array();
		foreach(\Util::get_mod_or_ctrl() as $k => $v):
			if ( ! isset($v['nicename']) || ! isset($v['admin_home']) || in_array($k, $exceptions)) continue;
			if ( ! property_exists($k, 'locomo')) continue;
			$controllers[\Inflector::ctrl_to_safestr($k)] = $k::$locomo['nicename'];
		endforeach;
//		$selected = isset($obj->ctrl) && ! empty($obj->ctrl) ? $obj->ctrl : $ctrl;
		\Arr::set($_properties, 'ctrl.form.options', $controllers);
		\Arr::set($_properties, 'ctrl.default', $ctrl);

		// title
//		$title = \Arr::get($controllers, $selected, @$obj->title);


		return $_properties;
	}

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory = 'help', $obj = NULL)
	{
		if (static::$_cache_form_definition && $obj == null) return static::$_cache_form_definition;

		//forge
		$form = \Fieldset::forge($factory, \Config::get('form'));

		// action
		$action = urlencode(\Input::get('action'));
		$ctrl = \Inflector::words_to_upper(substr($action, 0, strpos($action, '%')));

		// prepare options
		$actions = array('all' => '共通ヘルプ');
//		$exceptions = array('\\Help\\Controller_Help', '\\Admin\\Controller_Admin', '\\Content\\Controller_Content');
		$controllers = array();
		foreach(\Util::get_mod_or_ctrl() as $k => $v):
			if ( ! isset($v['nicename']) || ! isset($v['admin_home'])) continue;
			if ( ! property_exists($k, 'locomo')) continue;
			$controllers[\Inflector::ctrl_to_safestr($k)] = $k::$locomo['nicename'];
		endforeach;
		$selected = isset($obj->ctrl) && ! empty($obj->ctrl) ? $obj->ctrl : $ctrl;
		$form->add(
			'ctrl',
			'アクション',
			array('type' => 'select', 'style' => 'width: 30%;', 'options' => $controllers, 'class' => 'varchar')
		)
		->add_rule('required')
		->add_rule('max_length', 255)
		->set_value($selected);

		//title - 表題
		$title = \Arr::get($controllers, $selected, @$obj->title);
		$form->add(
			'title',
			'表題',
			array('type' => 'hidden', 'class' => 'varchar')
		)
		->add_rule('required')
		->add_rule('max_length', 255)
		->set_value($title);

		//body - 本文
		$form->add(
			'body',
			'本文',
			array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;', 'class' => 'text tinymce')
		)
		->add_rule('required')
		->set_value(@$obj->body);

		static::$_cache_form_definition = $form;
		return $form;
	}
}
