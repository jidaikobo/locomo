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
		'mod_or_ctrl',
		'body',
		'updated_at',
		'deleted_at',
		'creator_id',
		'modifier_id',
		'seq',
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
		'Revision\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save', 'before_delete'),
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
	public static function form_definition($factory = 'help', $obj = NULL)
	{
		if (static::$_cache_form_definition && $obj == null) return static::$_cache_form_definition;

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

		// mod_or_ctrl - コントローラ
		$mod_or_ctrl = '';
		$urls = parse_url(\Input::referrer());// parse referrer
		if (\Arr::get($urls, 'query', ''))
		{
			list($s, $q) = explode('[mod_or_ctrl]=', \Arr::get($urls, 'query', ''));// explode by key str
			// multiple query?
			if (strpos($q, '&') !== false)
			{
				list($mod_or_ctrl, $q) = explode('&', $q);
			}else{
				$mod_or_ctrl = $q;
			}
		}
		$mod_or_ctrls = array('all' => '共通ヘルプ');
		$exceptions = array('help', 'admin', 'content');
		foreach(\Util::get_mod_or_ctrl() as $k => $v):
			if ( ! isset($v['nicename']) || ! isset($v['admin_home']) || in_array($k, $exceptions)) continue;
			$key = substr($v['admin_home'], 0, strpos($v['admin_home'], '/'));
			$mod_or_ctrls[$k] = $v['nicename'];
		endforeach;
		$checked = isset($obj->mod_or_ctrl) && ! empty($obj->mod_or_ctrl) ? $obj->mod_or_ctrl : $mod_or_ctrl;

		$form->add(
			'mod_or_ctrl',
			'コントローラ',
			array('type' => 'select', 'style' => 'width: 30%;', 'options' => $mod_or_ctrls, 'class' => 'varchar')
		)
		->add_rule('required')
		->add_rule('max_length', 255)
		->set_value($checked);

		//body - 本文
		$form->add(
			'body',
			'本文',
			array('type' => 'textarea', 'rows' => 7, 'style' => 'width:100%;', 'class' => 'text tinymce')
		)
		->add_rule('required')
		->set_value(@$obj->body);

		//order - 
		$form->add(
			'seq',
			'表示順',
			array('type' => 'text', 'size' => 5, 'class' => 'int[5]')
		)
		->add_rule('required')
		->add_rule('max_length', 5)
		->set_value(@$obj->seq ?: 10);

		static::$_cache_form_definition = $form;
		return $form;
	}
}
