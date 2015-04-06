<?php 
	$repeat_kbs = $model_name::get_repeat_kbs();
	$detail_kbs = $model_name::get_detail_kbs();
	$importance_kbs = $model_name::get_importance_kbs();
	$currentday = (date("Y") == $year && date("n") == $mon ) ? date("j") : '';
?>
<h1><?php print $year; ?>年<?php print (int)$mon; ?>月 カレンダ</h1>


<div class="field_wrapper calendar">
<div class="select_period lcm_focus pagination" title="月を選択">
	<?php print htmlspecialchars_decode($prev_url); ?> 
	<?php print htmlspecialchars_decode($next_url); ?>
	<span class="select_num lcm_focus" title="各月を選択">
	<?php 
		for($i = 1; $i <= 12; $i++) {
			if ($i == $mon) {
	?>
		<span class="active"><?php print $i; ?></span>
	<?php
	} else {
	?>
	<span><a href="<?php print \Uri::create( $kind_name . '/calendar/' . $year . '/' . $i); ?>"><?php print $i; ?></a></span>
	<?php
	}
	?>
	<?php } ?>
	</span>
	<?php print htmlspecialchars_decode($prev_year_url); ?> 
	<?php print htmlspecialchars_decode($next_year_url); ?>

</div>

<div class="narrow_user lcm_focus" title="表示絞り込み">
<?php
//if (\Request::main()->controller == 'Controller_Scdl'):
	include("calendar_narrow.php");
//else:
//	include(APPPATH."modules/reserve/views/reserve/calendar_narrow.php");
//endif;
?>

</div>
<table class="calendar month lcm_focus" title="カレンダ">
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
<?php $detail_pop_array = array(); ?>
<?php foreach($schedule_data as $v): ?>
	<?php if ($v['week'] == 1) { print '<tr>'; } ?>
	<?php $class_str = 'week'.$v['week'];
		if(isset($v['day'])):
			$class_str.= $currentday == $v['day'] ? ' today' : '';
//			$class_str.= $currentday == $v['day'] ? ' holiday' : ''; //祝日のとき
			//each_date_title_strはフォーカス移動時読み上げ文字列
			//date_str, each_date_title_skip は枠内フォーカス時タイトル読み上げ文字列
			$each_date_title_str = $currentday == $v['day'] ? '今日 ' : '';
			$each_date_title_str .= $v['day'].'日'.$week_name[$v['week']].'曜日 ';
//			$each_date_title_str.=  ? '祝日 ' : '';//祝日の名前(振り替え休日のことも考えたほうがよいのかも)。
			$each_date_title_str .= (count($v['data']) > 0) ? count($v['data']) . '件の登録' : '登録なし';
			$date_str = $v['day'] < 10 ? '&nbsp;'.$v['day'] : $v['day'];
			$each_date_title_skip = $week_name[$v['week']] . '曜日';
//			$each_date_title_skip.=  ? '祝日</span><span class="holiday_name">'..'</span><span class="skip">' : '';//祝日の名前。
			$each_date_title_skip.= (count($v['data']) > 0) ? count($v['data']) . '件の登録' : '登録なし';
		else:
			$class_str.= '';
		endif;
	 ?>
	<td class="<?php echo $class_str ?>">
		<?php if (isset($v['day'])):?>
		<div class="each_date lcm_focus" title="<?php echo $each_date_title_str ?>">
			<a href="<?php echo \Uri::create($kind_name . "/calendar/" . sprintf("%04d/%02d/%02d/", $year, $mon, $v['day'])) ?>" class="title">
				<span class="date_str"><?php echo $date_str ?>日</span>
				<span class="skip"><?php echo $each_date_title_skip ?></span>
			</a>
			<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $v['day']))); ?>" class="add_new" title="新規追加"><span class="skip">新規追加</span></a>
			<div class="events">
			<?php foreach ($v['data'] as $v2):
				$detail_pop_data = $v2;
				$detail_pop_array[] = $v2;

				$eventtitle_icon = '';
				$eventtitle_skip = '<span class="skip">';
				//繰り返し区分
				$eventtitle_icon.= $v2['repeat_kb'] != 0 ? '<span class="text_icon schedule repeat_kb_'.$v2['repeat_kb'].'"></span>' : '';
				$eventtitle_skip.= $v2['repeat_kb'] != 0 ? $repeat_kbs[$v2['repeat_kb']] : '';
				// 時間
				$event_time_display_data = $model_name::make_target_day_info($v2);
				$event_time_display = (\Session::get('scdl_display_time') == "1") ? "inline" : "none";
				$event_time = '<span class="scdl_time" style="display:' . $event_time_display . '">[' . $event_time_display_data['start_time'] . "〜" . $event_time_display_data['end_time'] . ']</span>';
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

				echo '<p class="lcm_tooltip_parent" data-jslcm-tooltip-id="pop'.$v2->scdlid.$v2->target_year.$v2->target_mon.$v2->target_day.'">';

				echo '<a href="' . \Uri::create($kind_name . "/viewdetail/" . $v2['scdlid'] . sprintf("/%d/%d/%d", $v2['target_year'], $v2['target_mon'], $v2['target_day'])) . '">';

				echo $eventtitle_icon.$event_time.htmlspecialchars($v2['title_text']).$eventtitle_skip;
				echo '</a>';
				
				echo '</p>';
			endforeach;?>
			</div><!-- /.events -->
		</div><!-- /.each_date -->
		<?php endif; ?>
	</td>
	<?php if ($v['week'] == 0) { print '</tr>'; } ?>
<?php endforeach; ?>
</table>
	<div class="legend calendar" aria-hidden=true>
<?php
	foreach($repeat_kbs as $k => $v):
		echo $k != 0 ? '<span class="display_inline_block"><span class="text_icon schedule repeat_kb_'.$k.'"><span class="skip"> '.$v.'</span></span>'.$v.' </span>' : '';
	endforeach;
	foreach($detail_kbs as $k => $v):
		echo $k != 'unspecified_kb' ? '<span class="display_inline_block"><span class="text_icon schedule '.$k.'"><span class="skip"> '.$v.'</span></span>'.$v.' </span>' : '';
	endforeach;
	if(!\Request::is_hmvc()): //重要度
		foreach($importance_kbs as $k => $v):
			echo '<span class="display_inline_block"><span class="icon mark_importance"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$k.'.png" alt="'.$v.'"></span>'.$v.'</span>';
		endforeach;
	endif;
	echo $locomo['controller']['name'] === "\Controller_Scdl" ? '<span class="display_inline_block"><span class="icon mark_private"><img src="'.Uri::base().'lcm_assets/img/system/mark_private.png" alt="非公開"></span>非公開</span>' : '';
?>
	 </div><!-- /.legend.calendar -->
</div><!-- /.field_wrapper -->
<?php foreach($detail_pop_array as $v):
	$detail_pop_data = $v;
	include("detail_pop.php");
endforeach ;?>


