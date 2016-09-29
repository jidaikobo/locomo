<?php
namespace Locomo;
class Model_Msgbrd extends \Model_Base_Soft
{
//	use \Model_Traits_Wrkflw;

	// $_table_name
	protected static $_table_name = 'lcm_msgbrds';

	// $_conditions
	protected static $_conditions = array();
	public static $_options = array();

	// $_properties
	protected static $_properties = array(
		'id',
		'parent_id' => array('form' => array('type' => 'hidden'), 'default' => 0),
		'name' =>  array(
			'label' => '表題',
			'data_type' => 'varchar(255)',
			'form' => array(
				'type' => 'text',
				'size' => 30,
				'class' => 'varchar',
			),
			'validation' => array(
				'required',
				'max_length' => array (
					255,
				),
			),
		),
		'usergroup_id' => array(
			'label' => '公開範囲(グループ)',
			'form' => array(
				'type' => 'select',
			),
			'validation' => array (
				'required_least' => array(array('usergroup_id', 'user_id')),
			),
			'default' => null,
		),
		'user_id' => array(
			'label' => '公開範囲(個人)',
			'form' => array(
				'type' => 'select',
			),
			'validation' => array (
				'required_least' => array(array('usergroup_id', 'user_id')),
			),
		),
		'category_id' => array(
			'label' => 'カテゴリ',
			'form' => array(
				'type' => 'select',
			),
		),
		'contents' => array(
			'label' => '本文',
			'data_type' => 'text',
			'form' => array(
				'type' => 'textarea',
				'class' => 'textarea',
			),
			'validation' => array(
				'required',
			),
		),
		'is_sticky' => array(
			'label' => '先頭に固定表示',
			'data_type' => 'bool',
			'form' => array(
				'type' => 'select',
				'options' => 
				array (
					0 => 'しない',
					1 => '先頭に固定表示する',
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

		'expired_at' => array(
			'label' => '公開期限',
			'data_type' => 'datetime',
			'form' => 
			array (
				'type' => 'text',
				'class' => 'datetime',
			),
		),

		'created_at' => array(
			'label' => '作成日',
			'data_type' => 'datetime',
			'form' => array(
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
		),
		'user' => array(
			'key_from'         => 'user_id',
			'model_to'         => 'Model_Usr',
			'key_to'           => 'id',
			'cascade_save'     => false,
			'cascade_delete'   => false,
		),
		'parent' => array(
			'key_from'         => 'parent_id',
			'model_to'         => 'Model_Msgbrd',
			'key_to'           => 'id',
			'cascade_save'     => false,
			'cascade_delete'   => false,
		),
	);

	// $_has_many
	protected static $_has_many = array(
		'child' => array(
			'key_from'         => 'id',
			'model_to'         => 'Model_Msgbrd',
			'key_to'           => 'parent_id',
			'cascade_save'     => false,
			'cascade_delete'   => false,
		),
	);

	protected static $_many_many = array(
		'opened' => array(
			'key_from'         => 'id',
			'key_through_from' => 'msgbrd_id',
			'model_to'         => 'Model_Usr',
			'table_through'    => 'lcm_msgbrds_opened',
			'key_through_to'   => 'user_id',
			'key_to'           => 'id',
			'cascade_save'     => false,
			'cascade_delete'   => false,
		),
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
	}


	public function _event_before_save()
	{
		// usergroup 0 がゲストユーザーの為
		// 空文字で null をセットする
		if ( '' === \Input::post('usergroup_id', ''))
		{
			$this->usergroup_id = null;
		}
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

		// static::$_options
		// 管理者は下書き以外であっても見られる
		// また管理者は、他人の下書きも見られる（つまり管理者が見るときには余計な条件設定をしない）
		if ( ! \Auth::is_admin())
		{
			$options['where'][] = array(
				// draftでなく、公開範囲内なら許可
				array(
					array('is_draft', '=', '0'),
					array('usergroup_id', 'IN', \Auth::get_groups()),
				),
				 // 公開範囲ではないが、creator_idが一致する。下書きかどうかは問わない。
				'or' => array(
					// array('creator_id', 'IN', array(\Auth::get('id'), -1, -2)), ???
					array('creator_id', '=', \Auth::get('id')),

					// もしくは、自分個人宛て
					'or' => array(
						array('is_draft', '=', '0'),
						array('user_id', '=', \Auth::get('id')),
					),
				),
			);
		}

		$options['order_by'] = array(
			'is_sticky' => ' DESC',
			'created_at' => ' DESC',
		);

		// array_merge
		static::$_options = \Arr::merge(static::$_options, $options);

		//return
		return $options;
	}

	/**
	 * set_draft_options()
	 * @return array()
	 */
	public static function set_draft_options()
	{
		$options = static::set_public_options(array('is_draft', 'created_at', 'expired_at'));
		$options['where'][] = array('is_draft' => 1);

		// array_merge
		static::$_options = \Arr::merge(static::$_options, $options);

		//return
		return $options;
	}

	/**
	 * set_deleted_options()
	 * 自分専用のごみ箱
	 * @return array()
	 */
	public static function set_deleted_options()
	{
		$options = parent::set_deleted_options();

		// static::$_options
		// 管理者は、他人のごみ箱も見られる（つまり管理者が見るときには余計な条件設定をしない）
		if ( ! \Auth::is_admin())
		{
			$options['where'][] = array(
				array(
					array('creator_id', 'IN', array(\Auth::get('id'), -1, -2)),
				), 
			);
		}

		// array_merge
		static::$_options = \Arr::merge(static::$_options, $options);

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
		$options['where'][] = array(
			// draftでなく、公開範囲内なら許可
			array(
				array('is_draft', '=', '0'),
				array('usergroup_id', 'IN', \Auth::get_groups()),
			),
			 // 公開範囲ではないが、creator_idが一致する。下書きかどうかは問わない。
			'or' => array(
				// array('creator_id', 'IN', array(\Auth::get('id'), -1, -2)), ???
				array('creator_id', '=', \Auth::get('id')),

				// もしくは、自分個人宛て
				'or' => array(
					array('is_draft', '=', '0'),
					array('user_id', '=', \Auth::get('id')),
				),
			),
		);
	}

	/*
	 * 未読・既読
	 */
	public function is_opened()
	{
		if (\Auth::get('id') == $this->creator_id)
		{
			return true;
		}

		$cnt = Model_Msgbrd_Opened::count(array(
			'where' => array(
				array('user_id', \Auth::get('id')),
				array('msgbrd_id', $this->id),
			),
		));

		return ($cnt > 0);
	}

	/*
	 * 開封
	 */
}
