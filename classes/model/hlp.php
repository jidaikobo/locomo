<?php
namespace Locomo;
class Model_Hlp extends \Model_Base
{
	protected static $_table_name = 'lcm_hlps';

	protected static $_properties = array(
		'id',
		'title' => array(
			'lcm_role' => 'subject',
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
				'style' => 'width: 100%;',
				'rows' => '7',
				'class' => 'text tinymce',
			),
			'validation' => array(
//				'required',
			),
		),

		'creator_id' => array('form' => array('type' => false), 'default' => ''),
		'updater_id' => array('form' => array('type' => false), 'default' => ''),
		'created_at' => array('form' => array('type' => false), 'default' => null),
		'updated_at' => array('form' => array('type' => false), 'default' => null),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
	);



	//observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	protected static $_observers = array(
		'Orm\Observer_UpdatedAt' => array(
				'events' => array('before_update'),
				'mysql_timestamp' => true,
			),
		'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
		'Locomo\Observer_Revision' => array(
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
	public static function form_definition($factory = 'hlp', $obj = NULL)
	{
		//forge
		$form = parent::form_definition($factory, $obj);

		// action
		$action = urlencode(\Input::get('action'));
		$ctrl = \Inflector::words_to_upper(substr($action, 0, strpos($action, '%')));

		// prepare options
		$actions = array('all' => '共通ヘルプ');
		$controllers = array();
		foreach(\Util::get_mod_or_ctrl() as $k => $v):
			if ( ! isset($v['nicename']) || ! isset($v['main_action'])) continue;
			if ( ! \Util::get_locomo($k, 'nicename')) continue;
			$controllers[\Inflector::ctrl_to_safestr($k)] = $k::$locomo['nicename'];
		endforeach;
		$selected = isset($obj->ctrl) && ! empty($obj->ctrl) ? $obj->ctrl : $ctrl;
		$form->field('ctrl')
			->set_options($controllers)
			->set_value($selected);

		//title
		$title = \Arr::get($controllers, $selected, @$obj->title);
		$form->field('title')
			->set_value($title);

		return $form;
	}
}
