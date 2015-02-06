<?php
namespace Locomo;
class Model_Scdl extends \Model_Base
{
//	use \Model_Traits_Wrkflw;

	protected static $_table_name = 'lcm_scdls';

	public static $_kind_name = "scdl";
	public static $_kind_flg = 1;	// 1:スケジューラ 2:施設予約

	protected static $_properties =
	array (
		'id',
		'repeat_kb' => 
		array (
			'label' => '繰り返し区分',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'select',
				'size' => 0,
				'options' => array('0' => 'なし'
								, '1' => '毎日'
								, '2' => '毎日（土日除く)'
								, '3' => '毎週'
								, '4' => '毎月'
								, '5' => '毎年'),
				'class' => 'int',
			),
		),
		'target_month' => 
		array (
			'label' => '対象月',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'int',
			),
		),
		'target_day' => 
		array (
			'label' => '対象日',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'int',
			),
		),
		'start_date' => 
		array (
			'label' => '開始日',
			'data_type' => 'date',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'date',
			),
			'validation' => 
			array (
				'required',
			),
		),
		'end_date' => 
		array (
			'label' => '終了日',
			'data_type' => 'date',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'date',
			),
			'validation' => 
			array (
				'required',
			),
		),
		'start_time' => 
		array (
			'label' => '開始時間',
			'data_type' => 'time',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'time min15',
			),
			'validation' => 
			array (
				'required',
			),
		),
		'end_time' => 
		array (
			'label' => '終了時間',
			'data_type' => 'time',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'time min15',
			),
			'validation' => 
			array (
				'required',
			),
		),
		'week_kb' => 
		array (
			'label' => '繰り返し曜日',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'select',
				'options' => array('0' => '日'
								, '1' => '月'
								, '2' => '火'
								, '3' => '水'
								, '4' => '木'
								, '5' => '金'
								, '6' => '土'),
				'size' => 0,
				'class' => 'int',
			),
		),
		'delete_day' => 
		array (
			'label' => '部分削除日',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'text',
			),
		),
		'title_text' => 
		array (
			'label' => 'タイトル',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'text',
			),
			'validation' => 
			array (
				'required',
			),
		),
		'title_importance_kb' => 
		array (
			'label' => 'タイトル（重要度）',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'select',
				'size' => 0,
				'class' => 'text',
				'options' => array('↑高' => '↑高', '→中' => '→中', '↓低' => '↓低')
			),
			'validation' => 
			array (
				'required',
			),
		),
		'title_kb' => 
		array (
			'label' => 'タイトル（区分）',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'select',
				'options' => array('標準' => '標準', '社内' => '社内', '社外' => '社外', '外出' => '外出', '来社' => '来社', '個人' => '個人'),
				'size' => 0,
				'class' => 'text',
			),
		),
		'provisional_kb' => 
		array (
			'label' => '仮登録',
			'form' => 
			array (
				'type' => 'checkbox',
				'value' => 1
			),
		),
		'unspecified_kb' => 
		array (
			'label' => '時間指定なし',
			'form' => 
			array (
				'type' => 'checkbox',
				'value' => 1
			),
		),
		'allday_kb' => 
		array (
			'label' => '終日',
			'form' => 
			array (
				'type' => 'checkbox',
				'value' => 1
			),
		),
		'private_kb' => 
		array (
			'label' => '非公開',
			'form' => 
			array (
				'type' => 'checkbox',
				'value' => 1
			),
		),
		'overlap_kb' => 
		array (
			'label' => '重複チェック',
			'form' => 
			array (
				'type' => 'checkbox',
				'value' => 1
			),
		),
		 
		'message' => 
		array (
			'label' => 'メッセージ',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'textarea',
				'rows' => 7,
				'style' => 'width:100%;',
				'class' => 'text',
			),
			'validation' => 
			array (
				'required',
			),
		),
		'group_kb' => 
		array (
			'label' => '表示するグループフラグ',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'radio',
				'class' => 'int',
				'options' => array('1' => '全グループ', '2' => 'グループ指定')
			),
			'validation' => 
			array (
				'required',
			),
		),
		'group_detail' => 
		array (
			'label' => 'グループ指定',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'select',
				'size' => 0,
				'class' => 'text',
			),
		),
		'purpose_kb' => 
		array (
			'label' => '施設使用目的区分',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'select',
				'size' => 0,
				'class' => 'text',
				'options' => array('賃室' => '賃室')
			),
			'validation' => 
			array (
				'required',
			),
		),
		'purpose_text' => 
		array (
			'label' => '施設使用目的テキスト',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'text',
			),
		),
		'user_num' => 
		array (
			'label' => '施設利用人数',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'text',
				'size' => 0,
				'class' => 'int',
			),
		),
		'user_id' => 
		array (
			'label' => '作成者',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'select',
				'size' => 0,
				'class' => 'int',
			),
			'validation' => 
			array (
				'required',
			),
		),
		'attend_flg' => 
		array (
			'label' => '出席確認',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'checkbox',
				'options' => array('1' => 'dummy')
			),
		),
		'kind_flg' => 
		array (
			'label' => '予約区分(1:スケジュール 2:施設予約)',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'hidden'
			),
		),
		'created_at' => 
		array (
			'label' => '作成日時',
			'data_type' => 'date',
			'form' => 
			array (
				'type' => 'text',
				'size' => 20,
				'class' => 'date',
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
		'is_visible' => 
		array (
			'label' => '可視属性',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'select',
				'options' => 
				array (
					0 => '不可視',
					1 => '可視',
				),
				'class' => 'int',
				'default' => 1,
			),
		),
	) ;



	//$_option_options - see sample at \Model_Usrgrp
	public static $_option_options = array();


	protected static $_belongs_to = array(
		'create_user' => array(
			'key_from' => 'user_id',
			'model_to' => '\Model_Usr',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		),
	);

	protected static $_many_many = array(

		'user' => array(
				'key_from' => 'id',
				'key_through_from' => 'schedule_id',
				'table_through' => 'lcm_scdls_members',
				'model_to' => '\Model_Usr',
				'key_through_to' => 'user_id',
				'key_to' => 'id',
				'cascade_save' => false,
				'cascade_delete' => false
			),
		
		// ユーザー区分団体等
		'building' => array(
			'key_from' => 'id',
			'key_through_from' => 'schedule_id',
			'table_through' => 'lcm_scdls_buildings',
			'model_to' => '\Model_Item',
			'key_through_to' => 'building_id',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				array('category', '=', 'schedule_building'),
			),
		),

	);
	//observers
	protected static $_soft_delete = array(
		'deleted_field'   => 'deleted_at',
		'mysql_timestamp' => true,
	);

	protected static $_observers = array(
		
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
				'events' => array('before_save'),
				'mysql_timestamp' => true,
			),
		'Locomo\Observer_Scdl' => array(
				'events' => array('before_insert', 'before_save', 'after_insert', 'after_save'),
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
	public static function form_definition($factory = 'schedules', $obj = null)
	{
		$form = parent::form_definition($factory, $obj);

		$usergroups = \Model_Usrgrp::get_options(array('where' => array(array('is_available', true))), 'name');
		$form->field('group_detail')->set_options($usergroups);
		
		$form->field('kind_flg')->set_value(self::$_kind_flg);

		// 作成者
		$form->field('user_id')->set_options(Model_Usr::get_options(array(), 'username'));
		// 初期値
		if ($obj->id == null) {
			// 自分を選択する
			$form->field('user_id')->set_value(\Auth::get('id'));
		}

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
	public static function plain_definition($factory = 'schedules', $obj = null)
	{
		$form = static::form_definition($factory, $obj);
/*
		$form->field('created_at')
			->set_attribute(array('type' => 'text'));
*/

		return $form;
	}
}
