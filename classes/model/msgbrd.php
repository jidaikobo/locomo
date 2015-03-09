<?php
class Model_Msgbrd extends \Model_Base
{
//	use \Model_Traits_Wrkflw;

	// $_table_name
	protected static $_table_name = 'lcm_msgbrds';

	// $_conditions
	protected static $_conditions = array();

	// $_options
	public static $_options = array();

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
				'class' => 'text',
			),
		),

		'created_at' => 
		array (
			'label' => '作成日',
			'data_type' => 'datetime',
			'form' => 
			array (
				'type' => 'text',
				'class' => 'text',
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
				'events' => array('before_save'),
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
//		't'Locomo\Observer_Workflow' => array(
//			'events' => array('before_insert', 'before_save','after_load'),
//		),
//		't'Locomo\Observer_Revision' => array(
//			'events' => array('after_insert', 'after_save', 'before_delete'),
//		),

	);

	/**
	 * _init
	 */
	 public static function _init()
	{
// add_authorize_methods もここに書ける？

		// properties
		self::$_properties['created_at'] = array(
			'label' => '作成日/公開日',
			'form' => array(
				'type' => 'text',
				'class' => 'datetime',
			),
			'default' => date('Y-m-d H:i:s'),
		);

		self::$_properties['expired_at'] = array(
			'label' => '公開期限',
			'form' => array(
				'type' => 'text',
				'class' => 'datetime',
			),
		);

/*
		self::$_properties['hoge'] = array(
			'label' => 'test',
			'form' => array(
				'type' => 'select'
			)
		);
*/
		// usergroup_id
		$options = array('' => '選択してください', '0' => '一般公開', '-10' => 'ログインユーザすべて');
		$options+= \Model_Usrgrp::get_options(
			array(
				'where' => array(
					array('is_available', 1),
					array('is_for_acl', 0),
					'or' => array('customgroup_uid', \Auth::get('id'))
				),
				'order_by' => array('seq' => 'ASC', 'name' => 'ASC')),
				'name'
			);



echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;position:relative;z-index:9999">' ;
var_dump( $options ) ;
echo '</textarea>' ;
die();

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
	 * form_definition()
	 *
	 * @param str $factory
	 * @param int $id
	 *
	 * @return  obj
	 */
	public static function form_definition($factory = 'msgbrd', $obj = null)
	{
		if (static::$_cache_form_definition && $obj == null)
		{
			return static::$_cache_form_definition;
		}

		$form = parent::form_definition($factory, $obj);

		// usergroup_id
/*
		$options = array('' => '選択してください', '0' => '一般公開', '-10' => 'ログインユーザすべて');
		$options+= \Model_Usrgrp::get_options(array('where' => array(array('is_available', 1)), 'order_by' => array('seq' => 'ASC', 'name' => 'ASC')), 'name');
		$form->field('usergroup_id')
			->add_rule('required')
			->set_options($options)
			->set_value($obj->usergroup_id);
*/
		// categories
		$options = array('' => '選択してください');
		$options+= \Model_Msgbrd_Categories::get_options(array('where' => array(array('is_available', 1)), 'order_by' => array('seq' => 'ASC', 'name' => 'ASC')), 'name');
		$form->field('category_id')
			->set_options($options)
			->set_value($obj->category_id);

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
	public static function plain_definition($factory = 'msgbrd', $obj = null)
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
		$form = \Fieldset::forge('msgbrd_search_form', $config);

		// 検索
		$form->add(
			'all',
			'フリーワード',
			array('type' => 'text', 'value' => \Input::get('all'))
		);

		// wrap
		$parent = parent::search_form_base('');
		$parent->add_after($form, 'msgbrd_search_form', array(), array(), 'opener');

		return $parent;
	}

	/**
	 * add_authorize_methods()
	 */
	public static function add_authorize_methods()
	{
		if ( ! in_array('auth_msgbrd', static::$_authorize_methods))
		{
			static::$_authorize_methods[] = 'auth_msgbrd';
		}
	}

	/*
	 * auth_msgbrd()
	 */
	public static function auth_msgbrd($controller = null, $options = array(), $mode = null)
	{
		// draftカラムがなければ、対象にしない
		$column = \Arr::get(static::get_field_by_role('draft'), 'lcm_field', 'is_draft');
		if (! isset(static::properties()[$column])) return $options;

		if (in_array(\Auth::get('id'), [-1, -2])) return $options;

		// logic
		$options['where'][] = array(
			// draftでなく、公開範囲内か？
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

		return $options;
	}
}
