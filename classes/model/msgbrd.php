<?php
class Model_Msgbrd extends \Model_Base_Soft
{
//	use \Model_Traits_Wrkflw;

	// $_table_name
	protected static $_table_name = 'lcm_msgbrds';

	// $_conditions
	protected static $_conditions = array();

	// $_properties
	protected static $_properties =
	array (
		'id',
		'name' => 
		array (
			'label' => '表題',
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
		'usergroup_id' =>
		array(
			'label' => '公開範囲',
			'form' => array('type' => 'select')
		),
		'category_id' =>
		array(
			'label' => 'カテゴリ',
			'form' => array('type' => 'select')
		),
		'contents' => 
		array (
			'label' => '本文',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'textarea',
				'class' => 'textarea',
			),
			'validation' => 
			array (
				'required',
			),
		),
		'is_sticky' => 
		array (
			'label' => '先頭表示',
			'data_type' => 'bool',
			'form' => 
			array (
				'type' => 'select',
				'options' => 
				array (
					0 => 'ダッシュボード／先頭に固定表示しない',
					1 => 'ダッシュボード／先頭に固定表示する',
				),
				'class' => 'bool',
			),
			'default' => 0
		),
		'is_draft' => 
		array (
			'label' => '公開',
			'data_type' => 'bool',
			'form' => 
			array (
				'type' => 'select',
				'options' => 
				array (
					1 => '下書き',
					0 => '公開',
				),
				'class' => 'bool',
			),
			'default' => 0
		),

		'expired_at' => 
		array (
			'label' => '公開期限',
			'data_type' => 'datetime',
			'form' => 
			array (
				'type' => 'text',
				'class' => 'datetime',
			),
		),

		'created_at' => 
		array (
			'label' => '作成日',
			'data_type' => 'datetime',
			'form' => 
			array (
				'type' => 'text',
				'class' => 'datetime',
			),
		),

		'creator_id' => array('form' => array('type' => false), 'default' => ''),
		'updater_id' => array('form' => array('type' => false), 'default' => ''),
		'updated_at' => array('form' => array('type' => false), 'default' => null),
		'deleted_at' => array('form' => array('type' => false), 'default' => null),
	) ;


	// $_belongs_to
	protected static $_belongs_to = array(
		'usergroup' => array(
			'key_from' => 'usergroup_id',
			'model_to' => 'Model_Usrgrp',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		'categories' => array(
			'key_from' => 'category_id',
			'model_to' => 'Model_Msgbrd_Categories',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false
		)
	);

	// $_has_many
	protected static $_has_many = array(
	);

	// observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
		'Orm\Observer_UpdatedAt' => array(
				'events' => array('before_update'),
				'mysql_timestamp' => true,
			),
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
		'Locomo\Observer_Expired' => array(
				'events' => array('before_insert', 'before_save'),
				'properties' => array('expired_at'),
			),
			'Locomo\Observer_Userids' => array(
			'events' => array('before_insert', 'before_save'),
		),
//		't'Locomo\Observer_Revision' => array(
//			'events' => array('after_insert', 'after_save', 'before_delete'),
//		),
	);

	/**
	 * _init
	 */
	 public static function _init()
	{
		// set $_authorize_methods
		static::$_authorize_methods[] = 'auth_msgbrd';

		// parent
		parent::_init();

		// properties
		self::$_properties['expired_at'] = array(
			'label' => '公開期限',
			'form' => array(
				'type' => 'text',
				'class' => 'datetime',
			),
		);

		// usergroup_id
		$options = array('' => '選択してください', '0' => '一般公開', '-10' => 'ログインユーザすべて');
		$options+= \Model_Usrgrp_Custom::find_options();
		self::$_properties['usergroup_id'] = array(
			'label' => '公開範囲',
			'form' => array(
				'type' => 'select',
				'options' => $options,
			),
			'validation' => array(
				'required',
			),
			'default' => ''
		);
	}

	/**
	 * set_search_options()
	 */
	public static function set_search_options()
	{
		// free word search
		$all = \Input::get('all') ? '%'.\Input::get('all').'%' : '' ;
		if ($all)
		{
			static::$_options['where'][] = array(
				array('name', 'LIKE', $all),
				'or' => array(
					array('contents', 'LIKE', $all),
				)
			);
		}
	}

	/**
	 * set_public_options()
	 * @param array() $exception
	 * @return array()
	 */
	public static function set_public_options($exception = array())
	{
		$options = parent::set_public_options($exception);

		// $_options - is_draft
		if (empty($exception) || ! in_array('is_draft', $exception))
		{
			$options['where'][][] = array('is_draft', '=', false);
		}

		// array_merge
		static::$_options = \Arr::merge_assoc(static::$_options, $options);

		//return
		return $options;
	}

	/*
	 * auth_msgbrd()
	 */
	public static function auth_msgbrd($controller)
	{
		// draftカラムがなければ、対象にしない
		$column = \Arr::get(static::get_field_by_role('draft'), 'lcm_field', 'is_draft');
		if (! isset(static::properties()[$column])) return;
		if (in_array(\Auth::get('id'), [-1, -2])) return;

		// static::$_options
		static::$_options['where'][] = array(
			// draftでなく、公開範囲内なら許可
			array(
				array($column, '=', '0'),
				array('usergroup_id', 'IN', \Auth::get_groups()),
			), 
			 // draftでもcreator_idが一致しているか
			'or' => array(
				array($column, '=', '1'),
				array('creator_id', '=', \Auth::get('id')),
			)
		);
	}
}
