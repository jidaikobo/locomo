<?php 
	$repeat_kbs = $model_name::get_repeat_kbs();
	$detail_kbs = $model_name::get_detail_kbs();
	$importance_kbs = $model_name::get_importance_kbs();
	$currentday = (date("Y") == $year && date("n") == $mon ) ? date("j") : '';
?>
<?php if(!\Request::is_hmvc()): ?>
<h1><?php echo $year; ?>年 <?php echo (int)$mon; ?>月 週間カレンダ</h1>
<div class="select_display_type">
	<?php print htmlspecialchars_decode($display_month); ?><!-- / -->
	<?php // print htmlspecialchars_decode($display_week); ?>
</div>
<div class="field_wrapper calendar">
<div class="select_period" title="週の選択">
	<?php print htmlspecialchars_decode($prev_url); ?> / 
	<?php print htmlspecialchars_decode($next_url); ?>
</div>
<div class="narrow_user lcm_focus" title="表示絞り込み">
<?php include("calendar_narrow.php"); ?>
</div>
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
<?php foreach($schedule_data['member_list'] as $row) { ?>
	<tr class="lcm_focus" title="<?php echo  $row['model']->display_name?>">
		<th>
		<?php print $row['model']->display_name; ?>
		</th>
<?php 
echo '<textarea style="width:100%;height:200px;background-color:#fff;color:#111;font-size:90%;font-family:monospace;">' ;
var_dump( $row ) ;
echo '</textarea>' ;
 ?>
		<?php foreach($schedule_data['schedules_list'] as $schedule_row) {?>
			<?php 
			$class_str =  'week'.$schedule_row['week'];
			$class_str.= $currentday == $schedule_row['day'] ? ' today' : '';
//			$class_str.= $currentday ==  ? ' holiday' : ''; //祝日のとき
			//each_date_title_strはフォーカス移動時読み上げ文字列
			//date_str, each_date_title_skip は枠内タイトル読み上げ文字列
			
			$each_date_title_str = $currentday == $schedule_row['day'] ? '今日 ' : '';
			$each_date_title_str.=  $schedule_row['day'].'日 '.$week_name[$schedule_row['week']].'曜日 ';
//			$each_date_title_str.=  ? '祝日 ' : '';//祝日の名前(振り替え休日のことも考えたほうがよいのかも)。
			$schedule_num = 0;
			foreach($schedule_row['data'] as $v1):
				foreach($member_rowdata['data'] as $item_detail):
					if($item_detail['id'] == $v1['scdlid']) $schedule_num++;
				endforeach;
			endforeach;
			$each_date_title_str .= $schedule_num!=0 ? $schedule_num . '件の登録' : ' 登録なし';
			$date_str = $schedule_row['day'] < 10 ? '&nbsp;'.$schedule_row['day'] : $schedule_row['day'];
			$each_date_title_skip = $week_name[$schedule_row['week']] . '曜日';
//			$each_date_title_skip.=  ? '祝日</span><span class="holiday_name">'..'</span><span class="skip">' : '';
			$each_date_title_skip.= $schedule_num!=0 ? ' '.$schedule_num . '件の登録' : ' 登録なし';
 ?>
	
		
		<td class="<?php echo $class_str ?>">
			<div class="each_date lcm_focus" title="<?php echo $each_date_title_str ?>">
				<a href="<?php echo \Uri::create(Config::get('base_url') . $kind_name . '/calendar/' . sprintf("%04d/%02d/%02d/", $year, $mon, $schedule_row['day'])); ?>" class="title">
					<span class="date_str"><?php print $date_str ?>日</span>
					<span class="skip"><?php print $each_date_title_skip ?></span>
				</a>
				<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $schedule_row['day']))); ?>" class="add_new" title="新規追加"><span class="skip">新規追加</span></a>
			
				<div class="events">
					<?php
					foreach ($row as $member_rowdata) {
						if (!isset($member_rowdata['data'])) { continue; }
						if (isset($schedule_row['day'])) { ?>
								<?php foreach ($schedule_row['data'] as $v2) {
									foreach ($member_rowdata['data'] as $member_detail) {
										if ($member_detail['id'] == $v2['scdlid']) {

											$detail_pop_array[] = $v2;
											?>
											<p class="lcm_tooltip_parent" data-jslcm-tooltip-id="pop<?php echo $v2->scdlid.$v2->target_year.$v2->target_mon.$v2->target_day ?>">
										<?php
											//繰り返し区分
											echo $v2['repeat_kb'] != 0 ? '<span class="text_icon schedule repeat_kb_'.$v2['repeat_kb'].'"><span class="skip"> '.$repeat_kbs[$v2['repeat_kb']].'</span></span>' : '';
											//詳細区分
											foreach($detail_kbs as $key => $value){
												if($v2[$key]){
													 echo '<span class="text_icon schedule '.$key.'"><span class="skip">'.$value.'</span></span>';
												}
											}
											//重要度
											$importance_v = $model_name::value2index('title_importance_kb', html_entity_decode($v2['title_importance_kb']));
											echo '<span class="icon"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$importance_v.'.png" alt="'.$importance_kbs[$importance_v].'"></span>';
											print htmlspecialchars_decode($v2['link_detail']);
										?>
											</p>

									<?php }
										}
									}
								}
							}
					?>
				</div><!-- /.events -->
			</div><!-- /.each_date -->
		</td>
<?php
	}
	print "</tr>";
}
?>





	

</tr>
</tbody>
</table>
<?php if(!\Request::is_hmvc()): ?>
</div><!-- /.field_wrapper.calendar -->
<?php endif; ?>
	<div class="legend calendar">
<?php
	foreach($repeat_kbs as $k => $v){
		echo $k != 0 ? '<span class="display_inline_block"><span class="text_icon schedule repeat_kb_'.$k.'"><span class="skip"> '.$v.'</span></span>'.$v.' </span>' : '';
	}
	foreach($detail_kbs as $k => $v){
		echo $k != 'unspecified_kb' ? '<span class="display_inline_block"><span class="text_icon schedule '.$k.'"><span class="skip"> '.$v.'</span></span>'.$v.' </span>' : '';
	}
	if(!\Request::is_hmvc()): //重要度
		foreach($importance_kbs as $k => $v){
			echo '<span class="display_inline_block"><span class="icon mark_importance"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$k.'.png" alt="'.$v.'"></span>'.$v.'</span>';
		}
	endif;
//	echo $locomo['controller']['name'] === "\Controller_Scdl" ? '<span class="display_inline_block"><span class="icon mark_private"><img src="'.Uri::base().'lcm_assets/img/system/mark_private.png" alt="非公開"></span>非公開</span>' : '';
?>
	 </div><!-- /.legend.calendar -->
<?php foreach($detail_pop_array as $v) {
	$detail_pop_data = $v;
	include("detail_pop.php");
} ?>


