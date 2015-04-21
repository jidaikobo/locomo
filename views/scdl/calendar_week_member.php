<?php 
	$repeat_kbs = $model_name::get_repeat_kbs();
	$detail_kbs = $model_name::get_detail_kbs();
	$importance_kbs = $model_name::get_importance_kbs();
	$currentday = (date("Y") == $year && date("n") == $mon ) ? date("j") : '';
?>
<?php if(!\Request::is_hmvc()): ?>
<h1><?php echo $year; ?>年 <?php echo (int)$mon; ?>月 週間カレンダ</h1>
<?php include("calendar_narrow.php"); ?>
<div class="field_wrapper calendar">
<div class="select_period" title="週の選択">
	<?php print htmlspecialchars_decode($prev_url); ?> / 
	<?php print htmlspecialchars_decode($next_url); ?>
</div>
<h2 class="skip">カレンダ</h2>
<?php endif; ?>
<table class="calendar week lcm_focus" title="カレンダ">
<?php if(!\Request::is_hmvc()): ?>
<thead>
	<tr>
		<th></th>
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
<?php $detail_pop_array = array(); ?>
<?php foreach($schedule_data['member_list'] as $row): ?>
	<tr class="lcm_focus" title="<?php echo  $row['model']->display_name?>">
		<th>
			<?php print $row['model']->display_name; ?>
		</th>
		<?php
		foreach($schedule_data['schedules_list'] as $schedule_row):
			$class_str =  'week'.$schedule_row['week'];
			$class_str.= $currentday == $schedule_row['day'] ? ' today' : '';
//			$class_str.= $currentday ==  ? ' holiday' : ''; //祝日のとき
			//each_date_title_strはフォーカス移動時読み上げ文字列
			//date_str, each_date_title_skip は枠内タイトル読み上げ文字列
			
			$each_date_title_str = $currentday == $schedule_row['day'] ? '今日 ' : '';
			$each_date_title_str.=  $schedule_row['day'].'日 '.$week_name[$schedule_row['week']].'曜日 ';
//			$each_date_title_str.=  ? '祝日 ' : '';//祝日の名前(振り替え休日のことも考えたほうがよいのかも)。
			$schedule_num = 0;
			foreach($row as $member_rowdata):
				if (!isset($member_rowdata['data'])) continue;
				if(isset($schedule_row['day'])):
					foreach($schedule_row['data'] as $v1):
						foreach($member_rowdata['data'] as $item_detail):
							if($item_detail['id'] == $v1['scdlid']) $schedule_num++;
						endforeach;
					endforeach;
				endif;
			endforeach;
			$each_date_title_str .= $schedule_num!=0 ? $schedule_num . '件の登録' : ' 登録なし';
			$date_str = $schedule_row['day'] < 10 ? '&nbsp;'.$schedule_row['day'] : $schedule_row['day'];
			$each_date_title_skip = $week_name[$schedule_row['week']] . '曜日';
//			$each_date_title_skip.=  ? '祝日</span><span class="holiday_name">'..'</span><span class="skip">' : '';
			$each_date_title_skip.= $schedule_num!=0 ? ' '.$schedule_num . '件の登録' : ' 登録なし';
			?>
		<td class="<?php echo $class_str ?>">
			<div class="each_date lcm_focus" title="<?php echo $each_date_title_str ?>">
				<a href="<?php echo \Uri::create(Config::get('base_url') . $kind_name . '/calendar/' . sprintf("%04d/%02d/%02d/", $schedule_row['year'], $schedule_row['mon'], $schedule_row['day'])); ?>" class="title">
					<span class="date_str"><?php print $date_str ?>日</span>
					<span class="skip"><?php print $each_date_title_skip ?></span>
				</a>
				
				<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $schedule_row['year'], $schedule_row['mon'], $schedule_row['day'])).'&amp;member_id='.$row['model']->id); ?>" class="add_new" title="新規追加"><span class="skip">新規追加</span></a>
			
				<div class="events">
					<?php
					foreach ($row as $member_rowdata):
						if (!isset($member_rowdata['data'])) continue;
						if (isset($schedule_row['day'])):
							foreach ($schedule_row['data'] as $v2):
								foreach ($member_rowdata['data'] as $member_detail):
									if ($member_detail['id'] == $v2['scdlid']):
										$detail_pop_array[] = $v2;

										$eventtitle_icon = '';
										$eventtitle_skip = '<span class="skip">';
										//繰り返し区分
										$eventtitle_icon.= $v2['repeat_kb'] != 0 ? '<span class="text_icon schedule repeat_kb_'.$v2['repeat_kb'].'"></span>' : '';
										$eventtitle_skip.= $v2['repeat_kb'] != 0 ? $repeat_kbs[$v2['repeat_kb']] : '';
										//詳細区分
										foreach($detail_kbs as $key => $value):
											if($v2[$key]):
												$eventtitle_icon.= '<span class="text_icon schedule '.$key.'"></span>';
												$eventtitle_skip.= ' '.$value;
											endif;
										endforeach;
										//重要度
										$importance_v = $model_name::value2index('title_importance_kb', html_entity_decode($v2['title_importance_kb']));
										$eventtitle_icon.= '<span class="icon"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$importance_v.'.png" alt=""></span>';
										$eventtitle_skip.= ' '.$importance_kbs[$importance_v];
										$eventtitle_skip.= '</span>';
										// 時間
										$event_time_display_data = $model_name::make_target_day_info($v2);
										$event_time_display = (\Session::get('scdl_display_time') == "1") ? "inline" : "none";
										$event_time = '<span class="scdl_time sr_add bracket" style="display:' . $event_time_display . '">'. $event_time_display_data['start_time'] . '<span class="sr_replace to"><span>から</span></span>' . $event_time_display_data['end_time'] . '</span>';
					
										echo '<p class="lcm_tooltip_parent" data-jslcm-tooltip-id="pop'.$v2->scdlid.$v2->target_year.$v2->target_mon.$v2->target_day.'">';
										
										echo '<a href="' . \Uri::create($kind_name . "/viewdetail/" . $v2['scdlid'] . sprintf("/%d/%d/%d", $v2['target_year'], $v2['target_mon'], $v2['target_day'])) . '">';
										echo $eventtitle_icon.$event_time.htmlspecialchars($v2['title_text']).$eventtitle_skip;
										echo '</a>';
	
										echo '</p>';
									endif;
								endforeach;
							endforeach;
						endif;
					endforeach; ?>
				</div><!-- /.events -->
			</div><!-- /.each_date -->
		</td>
<?php	endforeach; ?>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php include("inc_legend.php"); //カレンダ凡例 ?>
<?php if(!\Request::is_hmvc()): ?>
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
;?>
