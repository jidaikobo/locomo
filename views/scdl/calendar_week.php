<?php
	$repeat_kbs = $model_name::get_repeat_kbs();
	$detail_kbs = $model_name::get_detail_kbs();
	$importance_kbs = $model_name::get_importance_kbs();
	$currentday = (date("Y") == $year && date("n") == $mon ) ? date("j") : '';
?>
<?php if( ! $is_hmvc): ?>
<h1><?php echo $year; ?>年 <?php echo (int)$mon; ?>月 週間カレンダ</h1>
<?php include("calendar_narrow.php"); ?>
<div class="field_wrapper calendar">

<?php
	// 週選択
	$week_select_html = '';
	$week_select_html.= '<div class="select_period" title="週の選択">';
	$week_select_html.= htmlspecialchars_decode($prev_url).' / ';
	$week_select_html.= htmlspecialchars_decode($next_url);
	$week_select_html.= '</div>';
	echo $week_select_html;
?>

<h2 class="skip">カレンダ</h2>
<?php endif; ?>
<table class="calendar week <?php if (!\Request::is_hmvc()) echo 'lcm_focus" title="カレンダ';?>">
<?php if( ! $is_hmvc): ?>
	<thead>
		<tr>
			<th class="week1"><span>月曜日</span></th>
			<th class="week2"><span>火曜日</span></th>
			<th class="week3"><span>水曜日</span></th>
			<th class="week4"><span>木曜日</span></th>
			<th class="week5"><span>金曜日</span></th>
			<th class="week6"><span>土曜日</span></th>
			<th class="week0"><span>日曜日</span></th>
		</tr>
	</thead>
<?php endif; ?>
<tbody>
<tr>
<?php $detail_pop_array = array(); ?>
<?php foreach($schedule_data['schedules_list'] as $v):
	if (isset($v['is_holiday']) && $v['is_holiday']) {
		$class_str = "week_holiday";
	} else {
		$class_str =  'week'.$v['week'];
	}
	$class_str.= $currentday == $v['day'] ? ' today' : '';
	//each_date_title_strはフォーカス移動時読み上げ文字列
	//date_str, each_date_title_skip は枠内タイトル読み上げ文字列
if (isset($v['day'])):
	$each_date_title_str = $currentday == $v['day'] ? '今日 ' : '';
	$each_date_title_str.= $v['day'].'日 '.$week_name[$v['week']].'曜日 ';
	$each_date_title_str.= (isset($schedule_row['is_holiday']) && $schedule_row['is_holiday']) ? '祝日 ' : '';//祝日の名前(振り替え休日のことも考えたほうがよいのかも)。

	$each_date_title_str .= (count($v['data']) > 0) ?  count($v['data']) . '件の登録' : ' 登録なし';
	$date_str = $v['day'] < 10 ? '&nbsp;'.$v['day'] : $v['day'];
	$each_date_title_skip = $week_name[$v['week']] . '曜日';
	$each_date_title_skip.= (isset($schedule_row['is_holiday']) && $schedule_row['is_holiday']) ? '祝日</span><span class="holiday_name">'.'祝日'.'</span><span class="skip">' : '';
	$each_date_title_skip.=  (count($v['data']) > 0) ? ' '. count($v['data']) . '件の登録' : ' 登録なし';
endif; ?>
	<td class="<?php print $class_str; ?>">
		<?php if (isset($v['day'])): ?>
			<div class="each_date lcm_focus" title="<?php echo $each_date_title_str ?>">
				<a href="<?php echo \Uri::create(Config::get('base_url') . $kind_name . '/calendar/' . sprintf("%04d/%02d/%02d/", $v['year'], $v['mon'], $v['day'])); ?>" class="title">
					<span class="date_str"><?php print $date_str ?>日</span>
					<span class="skip"><?php print $each_date_title_skip ?></span>
				</a>
				<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $v['year'], $v['mon'], $v['day']))); ?>" class="add_new" title="新規追加"><span class="skip">新規追加</span></a>

			<div class="events">
			<?php foreach ($v['data'] as $v2):
				$detail_pop_array[] = $v2;

					$eventtitle_icon = '';
					$eventtitle_skip = '<span class="skip">';

					//詳細区分
					foreach($detail_kbs as $key => $value):
						if($v2[$key]):
							$eventtitle_icon.= '<span class="text_icon schedule '.$key.'"></span>';
							$eventtitle_skip.= $value.' ';
						endif;
					endforeach;
					
					//外部表示(施設予約)
					if(\Request::active()->controller == "Reserve\Controller_Reserve"):
						$eventtitle_icon.= $v2['public_display']==2 ? '<span class="text_icon reserve public"></span>' : '';
						$eventtitle_skip.= $v2['public_display']==2 ? '外部表示 ' : '';
					endif;

					//繰り返し区分
					$eventtitle_icon.= $v2['repeat_kb'] != 0 ? '<span class="text_icon schedule repeat_kb_'.$v2['repeat_kb'].'"></span>' : '';
					$eventtitle_skip.= $v2['repeat_kb'] != 0 ? $repeat_kbs[$v2['repeat_kb']].' ' : '';
					// 時間
					$event_time_display_data = $model_name::make_target_day_info($v2);
					$event_time_display = (\Session::get('scdl_display_time') == "1") ? "inline" : "none";
					$event_time = '<span class="scdl_time sr_add bracket" style="display:' . $event_time_display . '">'. $event_time_display_data['start_time'] . '<span class="sr_replace to"><span>から</span></span>' . $event_time_display_data['end_time'] . '</span>';
					//代理登録
					if(($v2->user_id && $v2->updater_id)&&($v2->user_id != $v2->updater_id)):
						$eventtitle_icon.= '<span class="text_icon schedule dairi"></span>';
						$eventtitle_skip.= '代理登録 ';
					endif;
/*
					//重要度
					$importance_v = $model_name::value2index('title_importance_kb', html_entity_decode($v2['title_importance_kb']));
					$eventtitle_icon.= '<span class="icon" style="width: 1em;"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$importance_v.'.png" alt=""></span>';
					$eventtitle_skip.= ' '.$importance_kbs[$importance_v];
					$eventtitle_skip.= '</span>';
*/
					echo '<p class="lcm_tooltip_parent" data-jslcm-tooltip-id="pop'.$v2->scdlid.$v2->target_year.$v2->target_mon.$v2->target_day.'">';

					echo '<a href="' . \Uri::create($kind_name . "/viewdetail/" . $v2['scdlid'] . sprintf("/%d/%d/%d", $v2['target_year'], $v2['target_mon'], $v2['target_day'])) . '">';
					echo $eventtitle_icon.$event_time.htmlspecialchars($v2['title_text']).$eventtitle_skip;
					echo '</a>';

					echo '</p>';
				endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</td>
<?php endforeach; ?>
</tr>
</tbody>
</table>
<?php include("inc_legend.php"); //カレンダ凡例 ?>
<?php if( ! $is_hmvc): ?>
</div><!-- /.field_wrapper.calendar -->
<?php endif; ?>
<?php
if($detail_pop_array):
	echo '<div style="display: none;"><section class="detail_pop_wrapper">';
	echo '<h1>予定詳細一覧</h1>';
	foreach($detail_pop_array as $v):
		$detail_pop_data = $v;
		include("detail_pop.php");
	endforeach;
	echo '</section></div>';
endif;
?>
