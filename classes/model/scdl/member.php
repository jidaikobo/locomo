<?php
namespace Locomo;
class Model_Scdl_Member extends \Model_Base
{
//	use \Model_Traits_Wrkflw;

	protected static $_table_name = 'lcm_scdls_members';
	public static $_subject_field_name = 'SOME_TRAITS_USE_SUBJECT_FIELD_NAME';

	protected static $_properties =
	array (
		'id',
		'schedule_id' => 
		array (
			'label' => 'スケジュールID',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'int',
			),
			'validation' => 
			array (
			),
		),
		'user_id' => 
		array (
			'label' => 'メンバーID',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'int',
			),
			'validation' => 
			array (
			),
		),
		'created_at' => 
		array (
			'label' => '作成日',
			'data_type' => 'datetime',
			'form' => 
			array (
				'type' => 'datetime',
				'size' => 0,
				'class' => 'datetime',
			),
			'validation' => 
			array (

			),
		),
		'updated_at' => 
		array (
			'label' => '更新日',
			'data_type' => 'datetime',
			'form' => 
			array (
				'type' => 'datetime',
				'size' => 0,
				'class' => 'datetime',
			),
			'validation' => 
			array (
				'required',
			),
		),
		'deleted_at' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		)
	) ;



	//$_option_options - see sample at \Model_Usrgrp
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
*/
	protected static $_belongs_to = array(
		'user' => array(
						'key_from' => 'user_id',
						'model_to' => '\Model_Usr',
						'key_to' => 'id',
						'cascade_save' => false,
						'cascade_delete' => false,
					)
	);


	//observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	protected static $_observers = array(
	);

	/**
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory = 'schedule_items', $obj = null)
	{
		if(static::$_cache_form_definition && $obj == null)
		{
			return static::$_cache_form_definition;
		}

		$form = parent::form_definition($factory, $obj);

/*
		//add field
		$options = \Model_Name::get_options(array('where' => array(array('category', 'NAME'))), 'name');
		$form->add_after('objname', 'NAME', array('type' => 'checkbox', 'options' => $options), array(), 'user_type')
			->set_value(array_keys($obj->objname));

		//template set
		$form->field('field_name')
			->set_template("\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{field} <span>{description}</span> {error_msg} <input type=\"button\" value=\"VALUE\"></td>\n\t\t</tr>\n");
*/

		static::$_cache_form_definition = $form;
		return $form;
	}

	/**
	 * plain_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function plain_definition($factory = 'schedule_items', $obj = null)
	{
		$form = static::form_definition($factory, $obj);
/*
		$form->field('created_at')
			->set_attribute(array('type' => 'text'));
*/

		return $form;
	}
}
