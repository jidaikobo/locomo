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
				'options' => array('0' => 'なし'
								, '1' => '毎日'
								, '2' => '毎日(土日除く)'
								, '3' => '毎週'
								, '4' => '毎月'
								, '6' => '毎月(曜日指定)'
								, '5' => '毎年'),
				'class' => 'int',
				'title' => '繰り返し区分'
			),
		),
		'target_month' => 
		array (
			'label' => '対象月',
			'data_type' => 'int ar',
			'form' => 
			array (
				'type' => 'text',
				'class' => 'int',
				'size' => 3,
			),
		),
		'target_day' => 
		array (
			'label' => '対象日',
			'data_type' => 'int ar',
			'form' => 
			array (
				'type' => 'text',
				'class' => 'int',
				'size' => 3,
			),
		),
		'start_date' => 
		array (
			'label' => '開始日',
			'data_type' => 'date',
			'form' => 
			array (
				'type' => 'text',
				'size' => 14,
				'class' => 'date',
			),
		),
		'end_date' => 
		array (
			'label' => '終了日',
			'data_type' => 'date',
			'form' => 
			array (
				'type' => 'text',
				'size' => 14,
				'class' => 'date',
			),
		),
		'start_time' => 
		array (
			'label' => '開始時間',
			'data_type' => 'time',
			'form' => 
			array (
				'type' => 'text',
				'size' => 7,
				'class' => 'time min15',
			),
		),
		'end_time' => 
		array (
			'label' => '終了時間',
			'data_type' => 'time',
			'form' => 
			array (
				'type' => 'text',
				'size' => 7,
				'class' => 'time min15',
			),
			'default' => '21:00'
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
				'class' => 'int',
			),
		),
		'week_index' => 
		array (
			'label' => '第何週',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'select',
				'options' => array('1' => '1'
								, '2' => '2'
								, '3' => '3'
								, '4' => '4'
								, '5' => '5'
								),
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
				'class' => 'text',
			),
		),
		'title_text' => 
		array (
			'lcm_role' => 'subject',
			'label' => 'タイトル',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'text',
				'size' => 50,
				'class' => 'text',
			),
			'validation' => 
			array (
				'required',
			),
		),
		'title_importance_kb' => 
		array (
			'label' => '重要度',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'select',
				'class' => 'text',
				'options' => array('↑高' => '↑高', '→中' => '→中', '↓低' => '↓低'),
				'title' => '重要度',
			),
		),
		'title_kb' => 
		array (
			'label' => '区分',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'select',
				'options' => array('標準' => '標準', '社内' => '社内', '社外' => '社外', '外出' => '外出', '来社' => '来社', '個人' => '個人'),
				'class' => 'text',
				'title' => '区分',
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
				'type' => 'hidden',
				'value' => 0
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
			'default' => 1
		),
		 
		'message' => 
		array (
			'label' => 'メッセージ',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'textarea',
				'rows' => 7,
				'class' => 'text',
			),
			'validation' => 
			array (
			),
		),
		'group_kb' => 
		array (
			'label' => '表示するグループ',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'radio',
				'class' => 'int',
				'options' => array('1' => '全グループ', '2' => 'グループ指定')
			),
			'default' => '1'
		),
		'group_detail' => 
		array (
			'label' => 'グループ指定',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'select',
				'class' => 'text',
				'title' => 'グループ指定',
			),
		),
		'purpose_kb' => 
		array (
			'label' => '施設使用目的区分',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'select',
				'class' => 'text',
				'options' => array('賃室' => '賃室', '会議' => '会議')
			),

		),
		'purpose_text' => 
		array (
			'label' => '施設使用目的テキスト',
			'data_type' => 'text',
			'form' => 
			array (
				'type' => 'text',
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
				'class' => 'int ar',
				'size' => 6,
				'title' => '施設利用人数 半角数字で入力してください',
			),
		),
		'user_id' => 
		array (
			'label' => '作成者',
			'data_type' => 'int',
			'form' => 
			array (
				'type' => 'select',
				'class' => 'int',
				'title' => '作成者'
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
				'type' => 'hidden',
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
				'type' => 'hidden',
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
			'model_to' => '\Model_Scdl_Item',
			'key_through_to' => 'building_id',
			'key_to' => 'item_id',
			'cascade_save' => false,
			'cascade_delete' => false,
			'conditions' => array(
				'where'=>array(
				array('item_group', '=', 'building'),
				)
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
				'events' => array('before_insert', 'before_save', 'after_insert', 'after_save', 'after_delete'),
			),

		'\Observer_Revision' => array(
			'events' => array('after_insert', 'after_save'),
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
		$form->field('user_id')->set_options(Model_Usr::get_options(array('order_by' => 'pronunciation'), 'display_name'));
		

		//$form->field('user_id')->set_value(\Auth::get('id'));
		$form->field('is_visible')->set_value(1);

		// 初期値
		if ($obj->id == null) {
			// 自分を選択する
			$form->field('user_id')->set_value(\Auth::get('id'));
			// 重要度
			$form->field('title_importance_kb')->set_value("→中");
			if (\Input::get("ymd", "") == "") {
				$form->field('start_date')->set_value(date('Y-m-d'));
				$form->field('end_date')->set_value(date('Y-m-d'));
			}
			$form->field('start_time')->set_value('09:00');
			$form->field('end_time')->set_value('21:00');
		}

		if (\Input::get("ymd")) {
			$form->field('start_date')->set_value(\Input::get("ymd"));
			$form->field('end_date')->set_value(\Input::get("ymd"));
		}
		if (\Input::post()) {
			// 日付の自動判断
			if (\Input::post("end_date", "") == "" || \Input::post("end_date", "") == "0000-00-00") {
				if (\Input::post("repeat_kb") == 0) {
					$_POST['end_date'] = \Input::post("start_date");
				} else {
					$_POST['end_date'] = "2100-01-01";
				}
			}
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

	/**
	 * [value2index description]
	 * @param  [type] $key [description]
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	public static function value2index($key, $val) {
		$index = 0;
		foreach (self::$_properties[$key]['form']['options'] as $optKey => $optValue) {
			if ($val == $optKey)
				return $index;
			$index++;
		}
		return null;
	}

	/**
	 * [get_repeat_kbs description]
	 * @return [type] [description]
	 */
	public static function get_repeat_kbs() {
		return array('0' => 'なし', '1' => '毎日', '2' => '毎日(土日除く)', '3' => '毎週', '4' => '毎月', '6' => '毎月(曜日指定)', '5' => '毎年');
	}

	/**
	 * [get_detail_kbs description]
	 * @return [type] [description]
	 */
	public static function get_detail_kbs() {
		return array('provisional_kb' => '仮登録', 'unspecified_kb' => '時間指定なし', 'allday_kb' => '終日');
	}

	/**
	 * [get_importance_kbs description]
	 * @return [type] [description]
	 */
	public static function get_importance_kbs() {
		return array('重要度 高', '重要度 中', '重要度 低');
	}

	/**
	 * [display_target_day_info description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public static function display_target_day_info($data) {

		// date_detail
		$date_detail = self::make_target_day_info($data);
		

		return $date_detail['print'];
	}
	public static function make_target_day_info($data) {

		// date_detail
		$date_detail = array();

		// 表示加工
		$data->display_startdate = date('Y年n月j日', strtotime($data->start_date . " " . $data->start_time));
		if($data->repeat_kb == 0 && date('Y-n-j', strtotime($data->start_date)) ==  date('Y-n-j', strtotime($data->end_date))): //繰り返しなしのときに開始日と終了日が同じ場合は省略する
			$data->display_enddate = '';
		elseif(date('Y', strtotime($data->start_date)) ==  date('Y', strtotime($data->end_date))): //開始日と終了日の年が同じ場合は年を省略する
			$data->display_enddate = date('n月j日', strtotime($data->end_date . " " . $data->end_time));
		else:
			$data->display_enddate = date('Y年n月j日', strtotime($data->end_date . " " . $data->end_time));
		endif;
		$data->display_starttime = date('i', strtotime($data->start_time))==0 ?
			date('G時', strtotime($data->start_date . " " . $data->start_time)) :
			preg_replace("/時0/", "時", date('G時i分', strtotime($data->start_date . " " . $data->start_time)));
		$data->display_endtime = date('i', strtotime($data->end_time))==0 ?
			date('G時', strtotime($data->end_date . " " . $data->end_time)) :
			preg_replace("/時0/", "時", date('G時i分', strtotime($data->start_date . " " . $data->end_time)));

		$print = "";
		// 対象の日時
		if ($data->repeat_kb == 0) {
			if($data->allday_kb){
				$data->display_starttime = $data->display_endtime = '';
			}
			// 指定なし
			if ($data->allday_kb && $data->display_enddate=='') {
				$print .= $data->display_startdate;
			} else {
				$print .= $data->display_startdate . ' ' . $data->display_starttime . " 〜 " . $data->display_enddate . " " . $data->display_endtime;
			}
		} else {
			$print .= $data->target_year . "年" . $data->target_mon . "月" . $data->target_day . "日";
			$print .= '　' . $data->display_starttime . " 〜 " . $data->display_endtime;
		}
		$date_detail['display_target_date'] = $print;

		// 登録データ
		$week = array('日', '月', '火', '水', '木', '金', '土');
		$repeat_kbs = self::get_repeat_kbs($data->repeat_kb);
		$print .= "<p>■登録条件</p>";
		$print .= "<p>繰り返し：" . $repeat_kbs[$data->repeat_kb];
		
		$date_detail['display_repeat_kb'] = $repeat_kbs[$data->repeat_kb];
		if ($data->repeat_kb == 3){
			$print .= $week[$data->week_kb] . "曜日";
			$date_detail['display_repeat_kb'] = '毎週 '.$week[$data->week_kb] . "曜日";
		} else if ($data->repeat_kb == 4) {
			$print .= intval($data->target_day) . "日";
			$date_detail['display_repeat_kb'] .= intval($data->target_day) . "日";
		} else if ($data->repeat_kb == 5) {
			$print .= intval($data->target_mon) . "月" . intval($data->target_day) . "日";
			$date_detail['display_repeat_kb'] .= intval($data->target_mon) . "月" . intval($data->target_day) . "日";
		} else if ($data->week_kb != "" && $data->repeat_kb == 6) {
			$print .= "(";
			if ($data->week_index) {
				$print .= "第" . $data->week_index;
				$date_detail['display_repeat_kb'] = "毎月 第" . $data->week_index;
			} else {
				$print .= "毎週";
				$date_detail['display_repeat_kb'] .= "毎週";
			}
			$print .= $week[$data->week_kb] . "曜日)";
			$date_detail['display_repeat_kb'] .= $week[$data->week_kb] . "曜日";
		}
		$print .= "</p>";
		if ($data->repeat_kb == 0) {
			$print .= '<span class="display_inline_block">' . $data->display_startdate . " " . $data->display_starttime . ' 〜</span> <span class="display_inline_block">' . $data->display_enddate . " " . $data->display_endtime . "</span>";
			
//			$data->display_enddate = $data->display_startdate == $data->display_enddate ? '' : $data->display_enddate;
			$date_detail['display_period'] = '<span class="display_inline_block">'.$data->display_startdate . " " . $data->display_starttime . ' 〜</span> <span class="display_inline_block">' . $data->display_enddate . " " . $data->display_endtime.'</span>';
		} else {
			$print .= '<span class="display_inline_block">' . $data->display_startdate . ' 〜</span> <span class="display_inline_block">' . $data->display_enddate . "</span>";
			$print .= '<span class="display_inline_block">' . $data->display_starttime . ' 〜</span> <span class="display_inline_block">' . $data->display_endtime . "</span>";
			$date_detail['display_period'] = $data->display_startdate . " 〜 " . $data->display_enddate . " " . $data->display_starttime . " 〜 " . $data->display_endtime;
		}
		if($data->repeat_kb == 0 && $data->display_enddate == ''):
			$date_detail['display_period_day'] = '<span class="display_inline_block">'.$data->display_startdate.'</span>';
		else:
			$date_detail['display_period_day'] = '<span class="display_inline_block">'.$data->display_startdate . " 〜 " . $data->display_enddate.'</span>';
		endif;
		$date_detail['display_period_time'] = '<span class="display_inline_block">'.$data->display_starttime . " 〜 " . $data->display_endtime.'</span>';

		$date_detail['print'] = $print;
		return $date_detail;
	}
}
