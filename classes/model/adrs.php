<?php
class Model_Adrs extends \Model_Base
{
	protected static $_table_name = 'lcm_adrs';

	protected static $_properties =
	array (
		'id',
		'name' => array(
			'label' => '氏名',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
			'validation' => 
			array (
				'required',
				'max_length' => 
				array (
					255,
				),
			),
		),
		'kana' => 
		array (
			'label' => 'かな',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
			'validation' => 
			array (
				'required',
				'max_length' => 
				array (
					255,
				),
			),
		),
		'company_name' => 
		array (
			'label' => '会社名',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
		),
		'company_kana' => 
		array (
			'label' => '会社名かな',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
		),
		'tel' => 
		array (
			'label' => '電話番号',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
		),
		'fax' => 
		array (
			'label' => 'FAX番号',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
		),
		'mail' => 
		array (
			'label' => 'メールアドレス',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
		),
		'mobile' => 
		array (
			'label' => '携帯電話',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
		),
		'zip3' => 
		array (
			'label' => '郵便番号',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
		),
		'zip4' => 
		array (
			'label' => '郵便番号',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
		),
		'address' => 
		array (
			'label' => '住所',
			'data_type' => 'varchar(255)',
			'form' => 
			array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
		),
		'memo' => 
		array (
			'label' => '備考',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'textarea',
				'rows' => 7,
				'style' => 'width:100%;',
				'class' => 'text',
			),
		),

		'group_id' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		),

		'created_at' => 
		array (
			'label' => '作成日時',
			'data_type' => 'datetime',
			'form' => 
			array (
				'type' => 'text',
				'size' => 20,
				'class' => 'datetime',
			),
		),
		'updated_at' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		),
		'deleted_at' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		),
		'creator_id' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		),
		'updater_id' => 
		array (
			'form' => 
			array (
				'type' => false,
			),
		),
	) ;



	//$_conditions
	protected static $_conditions = array();
	public static $_options = array();
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
		'group' => array(
			'key_from' => 'group_id',
			'model_to' => 'Model_Adrsgrp',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
);


	//observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
		'Orm\Observer_UpdatedAt' => array(
				'events' => array('before_save'),
				'mysql_timestamp' => true,
			),
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
			'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
//		't'Locomo\Observer_Workflow' => array(
//			'events' => array('before_insert', 'before_save','after_load'),
//		),
//		't'Locomo\Observer_Revision' => array(
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
	public static function form_definition($factory = 'adrs', $obj = null)
	{
		if (static::$_cache_form_definition && $obj == null)
		{
			return static::$_cache_form_definition;
		}

		$form = parent::form_definition($factory, $obj);

		//Adrsgrp
		$options = \Model_Adrsgrp::get_options(
			array(
				'where' => array(array('is_available', 1)),
				'order_by' => array('seq' => 'ASC')
			),
			'name'
		);
		$form->add_after('adrsgrp', 'グループ', array('type' => 'select', 'options' => $options), array(), 'name')
			->set_value(@$obj->group_id);

		if ( ! \Auth::is_admin())
		{
			$form->field('is_visible')->set_type('hidden')->set_value($obj->is_visible ?: 1);

		}


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
	public static function plain_definition($factory = 'adrs', $obj = null)
	{
		$form = static::form_definition($factory, $obj);
/*
		$form->field('created_at')
			->set_attribute(array('type' => 'text'));
*/

		return $form;
	}

	/*
	 * search_form
	 */
	public static function search_form()
	{
		$config = \Config::load('form_search', 'form_search', true, true);
		$form = \Fieldset::forge('adrs_search_form', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// グループ
		$options = array('' => '選択してください');
		$options+= \Model_Adrsgrp::get_options(array('order_by' => array('name')), 'name');
		$form->add(
				'group',
				'ユーザグループ',
				array('type' => 'select', 'options' => $options)
			)
			->set_value(\Input::get('group'));

		// wrap
		$parent = parent::search_form_base('アドレス');
		$parent->add_after($form, 'adrs_search_form', array(), array(), 'opener');

		return $parent;
	}
}
