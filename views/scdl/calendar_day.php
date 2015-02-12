<?php if(!\Request::is_hmvc()): ?>
<h1><?php echo $year; ?>年 <?php echo (int)$mon; ?>月 <?php echo (int)$day; ?>日 カレンダ一日詳細</h1>
<div class="select_display_type">
	<?php print htmlspecialchars_decode($display_month); ?> / 
	<?php print htmlspecialchars_decode($display_week); ?>
</div>

移動<input type="text" name="move_date" value="<?php print sprintf("%04d-%02d-%02d", $year, $mon, $day); ?>" class="date" id="move_date" />

<div style="position: rerative; max-width: 1000px;">
<div class="select_period" title="前後の日へ移動">
	<?php print htmlspecialchars_decode($prev_url); ?> / 
	<?php print htmlspecialchars_decode($next_url); ?>
</div>
<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $day))); ?>" />新規追加</a>

<div class="narrow_user lcm_focus" title="絞り込み">
<?php include("calendar_narrow.php"); ?>
</div>

<?php if (isset($schedule_data['member_list']) && count($schedule_data['member_list']) > 0) { ?>
<table class="table schedule_day tbl">
<tbody>
<?php foreach ($schedule_data['member_list'] as $row) { ?>
		<tr>
		<th class="name" rowspan="2">
			<?php print $row['model']->display_name; ?>
		</th>
			<?php foreach($schedule_data['schedules_list'] as $v) {?>
			<td colspan="2" class="time">
				<?php print $v['hour']; ?>
			</td>
			<?php } ?>
		</tr>
		<tr>
			<?php foreach($schedule_data['schedules_list'] as $v) {?>
			<?php $p_active = false; $s_active = false; ?>
			
				<?php foreach ($v['data'] as $detail_data) {
					foreach ($row['data'] as $member_detail) {
						if ($member_detail->id == $detail_data->schedule_id) {
							if (($detail_data->primary))
								$p_active = true;
							if (($detail_data->secondary))
								$s_active = true;
							//print ' class="active"';
						}
					}
				}
				?>
			<td colspan="" class="<?php if ($p_active) { print "active"; } ?> bar">
			</td>
			<td colspan="" class="<?php if ($s_active) { print "active"; } ?> bar">
			</td>
			<?php } ?>
		</tr>
<?php } ?>
	</tbody>
</table>
<?php } ?>
<?php endif;//hmvcをとじる ?>

<?php if (isset($schedule_data['member_list']) && count($schedule_data['member_list']) > 0) { ?>
	<table class="table tbl datatable">
		<thead>
		<tr>
			<th class="min">
				氏名
			</th>
			<th>
				予定時刻
			</th>
			<th>
				内容
			</th>
			<?php if(!\Request::is_hmvc()): ?>
			<th>
				登録者
			</th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
<?php foreach ($schedule_data['member_list'] as $row) { ?>
		<tr>
			<th>
				<?php print $row['model']->display_name; ?>
			</th>
			<td>
				<?php foreach ($row['data'] as $detaildata) { ?>
				<div>

<?php
		$detaildata->display_startdate = date('Y年n月j日', strtotime($detaildata->start_date . " " . $detaildata->start_time));
		$detaildata->display_enddate = date('Y年n月j日', strtotime($detaildata->end_date . " " . $detaildata->end_time));
		$detaildata->display_starttime = preg_replace("/時0/", "時", date('G時i分', strtotime($detaildata->start_date . " " . $detaildata->start_time)));
		$detaildata->display_endtime = preg_replace("/時0/", "時", date('G時i分', strtotime($detaildata->end_date . " " . $detaildata->end_time)));

		if ($detaildata->repeat_kb == 0) {
//			echo $detaildata->display_startdate . " " . $detaildata->display_starttime . "〜" . $detaildata->display_enddate . " " . $detaildata->display_endtime;
			echo $detaildata->display_starttime . " 〜 " . $detaildata->display_endtime;
		} else {
//			echo sprintf("%d年%d月%d日", $year, $mon, $day) . " " . $detaildata->display_starttime . "〜" . $detaildata->display_endtime;
			echo $detaildata->display_starttime . " 〜 " . $detaildata->display_endtime;
			if ($detaildata->week_kb != "" && $detaildata->repeat_kb == 2) {
				echo "(";
				$week = array('日', '月', '火', '水', '木', '金', '土');
				if ($detaildata->week_index) {
					echo "第" . $detaildata->week_index;
				} else {
					echo "毎週";
				}
				echo $week[$detaildata->week_kb] . "曜日)";
			}
		}
?>
				</div>
				<?php } ?>

			</td>
			<td>
						<?php
				$repeat_kbs = array('0' => 'なし', '1' => '毎日', '2' => '毎日(土日除く)', '3' => '毎週', '4' => '毎月', '6' => '毎月(曜日指定)', '5' => '毎年');
				 foreach ($row['data'] as $detaildata) { ?>
				<div>
			<?php	
				echo $detaildata->repeat_kb != 0 ? '<span class="text_icon schedule repeat_kb_'.$detaildata->repeat_kb.'"><span class="skip"> '.$repeat_kbs[$detaildata->repeat_kb].'</span></span>' : '';
				if ($detaildata->provisional_kb) { print '<span class="text_icon schedule provisional_kb"><span class="skip">仮登録</span></span>'; };
			//	if ($detaildata->unspecified_kb) { print '<span class="text_icon schedule unspecified_kb"><span class="skip">時間指定なし</span></span>'; };
				if ($detaildata->allday_kb) { print '<span class="text_icon schedule allday_kb"><span class="skip">終日</span></span>'; };
				echo '<a href="'.\Uri::create($kind_name.'/viewdetail/').$detaildata->schedule_id.'">';
				echo (!\Request::is_hmvc()) ? $detaildata->title_importance_kb.' ' : '';
				echo $detaildata->title_text.'('.$detaildata->title_kb.')' ;
				echo '</a>';
			}
			?>
				</div>
			</td>
			<?php if(!\Request::is_hmvc()): ?>
			<td>
			<?php  foreach ($row['data'] as $detaildata) {
				echo '<div>'.$detaildata->create_user->display_name.'</div>';
			} ?>
			</td>
			<?php endif; ?>
		</tr>
<?php } ?>
		</tbody>
	</table>
	<div class="legend calendar">
	<?php $repeat_kbs = array('0' => 'なし', '1' => '毎日', '2' => '毎日(土日除く)', '3' => '毎週', '4' => '毎月', '6' => '毎月(曜日指定)', '5' => '毎年'); ?>
	<?php foreach($repeat_kbs as $k => $v){
		echo $k != 0 ? '<span class="text_icon schedule repeat_kb_'.$k.'"><span class="skip"> '.$v.'</span></span>'.$v.' ' : '';
	 }?>
		<span class="text_icon schedule provisional_kb"><span class="skip">仮登録</span></span>仮登録 
		<!--<span class="text_icon schedule unspecified_kb"><span class="skip">時間指定なし</span></span>時間指定なし-->
		<span class="text_icon schedule allday_kb"><span class="skip">終日</span></span>終日 
	</div><!-- /.legend.calendar -->
<?php }else{ ?>
予定の登録がありません
<?php } ?>
</div><!-- /.field_wrapper -->
<script>
$("#move_date").change(function(event){
	var target = $("#move_date").val().replace(/-/g, "/");
	location.href='/<?php print $kind_name; ?>/calendar/' + target;
});
</script>

