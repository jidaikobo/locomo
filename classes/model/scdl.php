<?php
namespace Locomo;
class Model_Scdl extends \Model_Base_Soft
{
//	use \Model_Traits_Wrkflw;

	protected static $_table_name = 'lcm_scdls';

	public static $_kind_name = "scdl";
	public static $_kind_flg = 1;	// 1:スケジューラ 2:施設予約

	public static $_is_someedit = false; //Observer_Scdlで使う。Controller_Action_Someeditでも。

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
				'title' => '繰り返し区分',
				'onchange' => 'change_repeat_kb_area()',
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
			'validation' => array(
				'required',
				'match_pattern' => array("/^[0-9\/-]+$/u"),
				'max_length' => array(10),
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
			'validation' => array(
				'required',
				'match_pattern' => array("/^[0-9\/-]+$/u"),
				'max_length' => array(10),
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
			'validation' => array(
				'required',
				'match_pattern' => array("/^[0-9\:]+$/u"),
				'max_length' => array(8),
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
			'validation' => array(
				'required',
				'match_pattern' => array("/^[0-9\:]+$/u"),
				'max_length' => array(8),
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
				'title' => '曜日',
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
				'title' => '第何週',
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
/*
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
*/
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
				'value' => 1,
				'onchange' => 'is_allday()',
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
				'rows' => 3,
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
				'class' => 'text w10em',
				'title' => 'グループ指定',
				'onchange' => 'form_group_detail_change()',
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
				'options' => array('貸室' => '貸室', '会議' => '会議')
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
		'parent_id' =>
		array (
			'label' => '部分編集元親ID',
			'data_type' => 'int',
			'form' =>
			array (
				'type' => 'hidden'
			),
		),
		'creator_id' => array('form' => array('type' => false), 'default' => '', 'lcm_role' => 'creator_id'),
		'updater_id' => array('form' => array('type' => false), 'default' => ''),
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
		"Orm\Observer_Self" => array(),
		'Locomo\Observer_Creatorid' => array(
			'events' => array('before_insert'),
		),
		'Locomo\Observer_Updaterid' => array(
			'events' => array('before_save'),
		),
		'Locomo\Observer_Created' => array(
			'events' => array('before_insert', 'before_save'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
				'events' => array('before_update'),
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
	 * _event_before_save()
	 */
	public function _event_before_save()
	{
		$this->user_id = is_null($this->user_id) ? \Auth::get('id') : $this->user_id;
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
		return array('0' => 'なし', '1' => '毎日', '2' => '毎日(土日除く)', '3' => '毎週', '4' => '毎月', '6' => '毎月', '5' => '毎年');
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
			$data->display_enddate = $data->display_enddate == "" ? "" : $data->display_enddate." ";
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
				$print .= $data->display_startdate . " " . $data->display_starttime . "〜" . $data->display_enddate . " " . $data->display_endtime;
			}
		} else {
			$print .= $data->target_year . "年" . $data->target_mon . "月" . $data->target_day . "日";
			$print .= '　' . $data->display_starttime . "〜" . $data->display_endtime;
		}
		$date_detail['display_target_date'] = $print;
		//実使用時間

		if(!\Request::is_hmvc() && \Request::active()->controller == "Reserve\Controller_Reserve" && ($data->public_start_time!=0 || $data->public_end_time!=0)):
			$start_time = $data->public_start_time!=0 ? $data->public_start_time : $data->start_time;
			$start_time_hour   = date('G',strtotime('1974-12-25 '.$start_time)).'時';
			$start_time_minute = intval(date('i',strtotime('1974-12-25 '.$start_time)));
			$start_time_minute = $start_time_minute ? $start_time_minute.'分' : '';
			$end_time   = $data->public_end_time!=0 ? $data->public_end_time : $data->end_time;
			$end_time_hour   = date('G',strtotime('1974-12-25 '.$end_time)).'時';
			$end_time_minute = intval(date('i',strtotime('1974-12-25 '.$end_time)));
			$end_time_minute = $end_time_minute ? $end_time_minute.'分' : '';
			$print.= '（実使用時間：';
			$print.= $start_time_hour.$start_time_minute;
			$print.= '<span class="sr_replace to"><span class="skip">から</span></span>';
			$print.= $end_time_hour.$end_time_minute;
			$print.= '）';
	endif;

		// 登録データ
		$week = array('日', '月', '火', '水', '木', '金', '土');
		$repeat_kbs = self::get_repeat_kbs($data->repeat_kb);
		if($data->repeat_kb != 0) {
			$print .= "<td></tr><tr><th>期間：</th><td>";
			$print .= "<p>" . $repeat_kbs[$data->repeat_kb];
		}
		$date_detail['display_repeat_kb'] = $repeat_kbs[$data->repeat_kb];
		if ($data->repeat_kb == 3){ // 毎週
			$print .= $week[$data->week_kb] . "曜日";
			$date_detail['display_repeat_kb'] = '毎週 '.$week[$data->week_kb] . "曜日";
		} else if ($data->repeat_kb == 4) { // 毎月
			$print .= intval($data->target_day) . "日";
			$date_detail['display_repeat_kb'] .= intval($data->target_day) . "日";
		} else if ($data->repeat_kb == 5) { //毎年
			$print .= intval($data->target_mon) . "月" . intval($data->target_day) . "日";
			$date_detail['display_repeat_kb'] .= intval($data->target_mon) . "月" . intval($data->target_day) . "日";
		} else if ($data->week_kb != "" && $data->repeat_kb == 6) { //毎月（曜日指定）
			if ($data->week_index) {
				$print .= "第" . $data->week_index;
				$date_detail['display_repeat_kb'] = "毎月 第" . $data->week_index;
			} else {
				$print .= "毎週";
				$date_detail['display_repeat_kb'] .= "毎週";
			}
			$print .= $week[$data->week_kb] . "曜日";
			$date_detail['display_repeat_kb'] .= $week[$data->week_kb] . "曜日";
		}
		if($data->repeat_kb != 0) $print .= "</p>";
		if ($data->repeat_kb == 0) { //繰り返しなし
			//$print .= '<span class="display_inline_block">' . $data->display_startdate . " " . $data->display_starttime . '〜</span><span class="display_inline_block">' . $data->display_enddate . " " . $data->display_endtime . "</span>";

			$date_detail['display_period'] = '<span class="display_inline_block">'.$data->display_startdate . " " . $data->display_starttime . '〜</span><span class="display_inline_block">' . $data->display_enddate . " " . $data->display_endtime.'</span>';
		} else if($data->repeat_kb != 0){
			$print .= '<span class="display_inline_block">' . $data->display_startdate . '〜</span><span class="display_inline_block">' . $data->display_enddate . "</span>";
			$print .= ' <span class="display_inline_block">' . $data->display_starttime . '〜</span><span class="display_inline_block">' . $data->display_endtime . "</span>";
			$date_detail['display_period'] = $data->display_startdate . "〜" . $data->display_enddate . " " . $data->display_starttime . "〜" . $data->display_endtime;
		}
		if($data->repeat_kb == 0 && $data->display_enddate == ''):
			$date_detail['display_period_day'] = '<span class="display_inline_block">'.$data->display_startdate.'</span>';
		else:
			$date_detail['display_period_day'] = '<span class="display_inline_block">'.$data->display_startdate . "〜" . $data->display_enddate.'</span>';
		endif;
		$date_detail['display_period_time'] = '<span class="display_inline_block">'.$data->display_starttime . "〜" . $data->display_endtime.'</span>';


		// 時間を追加
		$date_detail['start_time'] = $data->display_starttime;
		$date_detail['end_time'] = $data->display_endtime;

		$date_detail['print'] = $print;
		return $date_detail;
	}
}
