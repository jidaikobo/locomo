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
<div class="narrow_user lcm_focus" title="絞り込み">
<?php include("calendar_narrow.php"); ?>
</div>
<?php endif; ?>
<table class="calendar week lcm_focus" title="カレンダ">
<?php if(!\Request::is_hmvc()): ?>
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
<?php $repeat_kbs = array('0' => 'なし', '1' => '毎日', '2' => '毎日(土日除く)', '3' => '毎週', '4' => '毎月', '6' => '毎月(曜日指定)', '5' => '毎年'); ?>
<?php foreach($schedule_data as $v) {?>
	<td class="week<?php print $v['week']; ?>">
		<?php if (isset($v['day'])) { ?>
		<div class="each_date">
				<a href="<?php echo \Uri::create(Config::get('base_url') . $kind_name . '/calendar/' . sprintf("%04d/%02d/%02d/", $year, $mon, $v['day'])); ?>" class="title">
				<span class="date_str"><?php print (int)$v['day']; ?>日</span>
				<span class="skip"><?php print $week_name[$v['week']] . '曜日'; ?> <?php if (count($v['data']) > 0) { print count($v['data']) . '件の登録';} else { print '登録なし'; } ?></span>
			</a>
			<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $v['day']))); ?>" class="add_new"><span class="skip">新規追加</span></a>
			
			<div class="events">
			<?php foreach ($v['data'] as $v2) {
				$detail_pop_data = $v2;
				?>
				<p class="lcm_tooltip_parent" data-jslcm-tooltip-id="pop<?php echo $detail_pop_data->scdlid ?>">
					<?php echo $v2['repeat_kb'] != 0 ? '<span class="text_icon schedule repeat_kb_'.$v2['repeat_kb'].'"><span class="skip"> '.$repeat_kbs[$v2['repeat_kb']].'</span></span>' : '';
						if ($v2['allday_kb']) { print '<span class="text_icon schedule allday_kb"><span class="skip">終日</span></span>'; };
						if ($v2['unspecified_kb']) { print '<span class="text_icon schedule unspecified_kb"><span class="skip">時間指定なし</span></span>'; };
						if ($v2['provisional_kb']) { print '<span class="text_icon schedule provisional_kb"><span class="skip">仮登録</span></span>'; };
						print htmlspecialchars_decode($v2['link_detail']);
					?>
				</p>
			<?php // include("detail_pop.php"); ?>
			<?php } ?>
			</div>
		</div>
		<?php } ?>
	</td>
<?php } ?>
</tr>
</tbody>
</table>
<div class="legend calendar">
<?php foreach($repeat_kbs as $k => $v){
	echo $k != 0 ? '<span class="text_icon schedule repeat_kb_'.$k.'"><span class="skip"> '.$v.'</span></span>'.$v.' ' : '';
 }?>
	<span class="text_icon schedule provisional_kb"><span class="skip">仮登録</span></span>仮登録 
	<!--<span class="text_icon schedule unspecified_kb"><span class="skip">時間指定なし</span></span>時間指定なし-->
	<span class="text_icon schedule allday_kb"><span class="skip">終日</span></span>終日 
</div><!-- /.legend.calendar -->
<?php if(!\Request::is_hmvc()): ?>
</div><!-- /.field_wrapper.calendar -->
<?php endif; ?>
<?php foreach($schedule_data as $v) {?>
	<?php if (isset($v['day'])) { ?>
		<?php foreach ($v['data'] as $v2) {
				$detail_pop_data = $v2;
				include("detail_pop.php");
		}?>
	<?php } ?>
<?php } ?>


