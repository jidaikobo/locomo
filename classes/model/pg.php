<?php
namespace Locomo;
class Model_Pg extends \Model_Base_Soft
{
//	use \Model_Traits_Wrkflw;

	// $_table_name
	protected static $_table_name = 'lcm_pgs';

	// $_conditions
	protected static $_conditions = array();

	// $_options
	public static $_options = array();

	// $_properties
	protected static $_properties =
	array (
		'id',
		'title' => array (
			'label' => '表題',
			'data_type' => 'text',
			'form' => array (
				'type' => 'textarea',
				'rows' => 2,
				'description' => '<code title="sup, sub">上下付き文字</code>、<code title="cite">イタリック</code>などを設定できますが、<code title="ブラウザの表題や検索エンジンの検索結果">title</code>などでは表示されません',
			),
			'validation' => array (
				'required',
			),
			'default' => '',
		),

		'path' => array (
			'label' => 'ファイル名',
			'data_type' => 'varchar',
			'form' => array (
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
				'placeholder' => 'example/example.html',
			),
			'validation' => array (
				'required',
//				'valid_string' => array('dots', 'alpha', 'numeric', 'dashes'),
				'match_pattern' => array('/^[0-9a-zA-Z\._-]+$/'),
				'max_length' => array (255),
				// rule added at presenter \Locomo\Presenter_Pg_Edit()
			),
		),

		'lang' => array (
			'label' => '言語',
			'data_type' => 'varchar',
			'form' => array (
				'type' => 'select',
			),
			'default' => '',
			'validation' => array (
				'valid_string' => array('alpha'),
				'required',
				// rule added at presenter \Locomo\Presenter_Pg_Edit()
			),
		),

		'content' => array (
			'label' => '本文',
			'data_type' => 'text',
			'form' => array (
				'type' => 'textarea',
				'rows' => 10,
				'class' => 'text',
			),
			'validation' => array (
				'required',
			),
			'default' => '',
		),
		'summary' => array (
			'label' => '要約',
			'data_type' => 'text',
			'form' => array (
				'type' => 'textarea',
				'rows' => 5,
				'class' => 'text',
			),
			'validation' => array (
//				'required',
			),
			'default' => '',
		),
		'url' => array (
			'label' => '転送先URL',
			'data_type' => 'text',
			'form' => array (
				'type' => 'text',
				'size' => 0,
				'style' => 'width:100%;',
				'description' => '<code>http://</code>から入力してください。このページへのアクセスを転送します',
			),
			'validation' => array (
//				'required',
			),
			'default' => '',
		),
		'lat' => array (
			'label' => '緯度',
			'data_type' => 'decimal[8,6]',
			'form' => array (
				'type' => 'text',
				'size' => 0,
				'class' => 'decimal[8,6]',
				'template' => 'opener',
			),
			'default' => null,
		),
		'lng' => array (
			'label' => '経度',
			'data_type' => 'decimal[9,6]',
			'form' => array (
				'type' => 'text',
				'size' => 0,
				'class' => 'decimal[9,6]',
				'template' => 'closer',
			),
			'default' => null,
		),
		'created_at' => array (
			'label' => '作成日',
			'data_type' => 'datetime',
			'form' => array (
				'type' => 'text',
				'size' => 20,
				'class' => 'datetime',
				'description' => '未来の日付を入れると予約項目になります',
			),
			'default' => null,
		),
		'deleted_at' => array (
			'label' => '削除日',
			'form' => array (
				'type' => false,
			),
		),
		'expired_at' => array (
			'label' => '公開終了',
			'data_type' => 'datetime',
			'form' => array (
				'type' => 'text',
				'size' => 20,
				'class' => 'datetime',
				'description' => '入力しなければ、公開期日はありません',
			),
			'default' => null,
		),
		'is_sticky' => array (
			'label' => '固定表示',
			'data_type' => 'bool',
			'form' => array (
				'type' => 'select',
				'options' => array (
					0 => '固定表示しない',
					1 => '固定表示する',
				),
				'class' => 'bool',
				'description' => '日付降順の一覧表などでも先頭に表示されるようになります',
			),
			'default' => 0,
		),
		'is_visible' => array (
			'label' => '一般公開',
			'data_type' => 'bool',
			'form' => array (
				'type' => 'select',
				'options' => array (
					0 => '一般公開しない',
					1 => '一般公開する',
				),
				'class' => 'bool',
			),
			'default' => 1,
		),
		'is_available' => array (
			'label' => '下書き',
			'data_type' => 'bool',
			'form' => array (
				'type' => 'select',
				'options' => array (
					0 => '下書き',
					1 => '一般公開',
				),
				'class' => 'bool',
			),
			'default' => 1,
		),
		'creator_id' => array (
			'label' => '作者',
			'form' => array (
				'type' => false,
			),
		),
		'updater_id' => array (
			'label' => '更新担当者',
			'form' => array (
				'type' => false,
			),
		),
		'workflow_status' => array (
			'form' => array (
				'type' => false,
			),
		),
	) ;

	protected static $_many_many = array(
		// 抽出用
		'pggrp' => array(
			'key_from' => 'id',
			'key_through_from' => 'pg_id',
			'table_through' => 'lcm_pg_pggrps',
			'key_through_to' => 'pggrp_id',
			'model_to' => '\Model_Pggrp',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
		// 取得用
		'pggrps' => array(
			'key_from' => 'id',
			'key_through_from' => 'pg_id',
			'table_through' => 'lcm_pg_pggrps',
			'key_through_to' => 'pggrp_id',
			'model_to' => '\Model_Pggrp',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	// observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	protected static $_observers = array(
		"Orm\Observer_Self" => array(),
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
	);

	/**
	 * _init
	 */
	 public static function _init()
	{
		// set $_authorize_methods
		// static::$_authorize_methods[] = 'auth_pg';

		// do something before call parent::_init()

		// parent - this must be placed at the end of _init()
		parent::_init();
	}

	/**
	 * set_search_options()
	 */
	public static function set_search_options()
	{
		// free word search
/*
		$all = \Input::get('all') ? '%'.\Input::get('all').'%' : '' ;
		if ($all)
		{
			static::$_options['where'][] = array(
				array('name', 'LIKE', $all),
				'or' => array(
					array('body', 'LIKE', $all),
					'or' => array(
						array('memo', 'LIKE', $all),
					)
				)
			);
		}
*/
	}
}
