<h1><?php print $year; ?>年<?php print (int)$mon; ?>月カレンダ</h1>
<div class="select_display_type">
	<?php print htmlspecialchars_decode($display_month); ?> / 
	<?php print htmlspecialchars_decode($display_week); ?>
</div>

<div class="field_wrapper calendar">
<div class="select_period lcm_focus" title="月を選択">
	<?php print htmlspecialchars_decode($prev_url); ?> 
	<?php print htmlspecialchars_decode($next_url); ?>
	<span class="select_num lcm_focus" title="各月を選択">
	<?php 
		for($i = 1; $i <= 12; $i++) {
			if ($i == $mon) {
	?>
		<span><?php print $i; ?></span>
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



<div class="narrow_user lcm_focus" title="絞り込み">
<?php include("calendar_narrow.php"); ?>
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
<?php foreach($schedule_data as $v) { ?>
	<?php if ($v['week'] == 1) { print '<tr>'; } ?>
	<td class="week<?php print $v['week']; ?>">
		<?php if (isset($v['day'])) { ?>
		<div class="each_date lcm_focus" title="<?php
			print $v['day'].'日 '.$week_name[$v['week']].'曜日 ';
				if (count($v['data']) > 0) {
					print count($v['data']) . '件の登録';
				} else {
					print '登録なし';
				}
			?>">
			<a href="<?php echo \Uri::create($kind_name . "/calendar/" . sprintf("%04d/%02d/%02d/", $year, $mon, $v['day'])) ?>" class="title">
				<span class="date_str"><?php print $v['day']; ?>日</span>
				<span class="skip"><?php print $week_name[$v['week']] . '曜日'; ?> <?php if (count($v['data']) > 0) { print count($v['data']) . '件の登録';} else { print '登録なし'; } ?></span>
			</a>
			<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $v['day']))); ?>" class="add_new"><span class="skip">新規追加</span></a>
			<?php /*?><div>
			<?php print count($v['data']); ?>件
			</div><?php */ ?>
			<div class="events">
			<?php foreach ($v['data'] as $v2) {
				$detail_pop_data = $v2;
				?>
				<p class="lcm_tooltip_parent" data-jslcm-tooltip-id="pop<?php echo $detail_pop_data->scdlid ?>">
					<span class="icon_small">
					<?php if ($v2['provisional_kb']) { print '[仮登録]'; }; ?>
					<?php if ($v2['unspecified_kb']) { print '[時間指定なし]'; }; ?>
					<?php if ($v2['allday_kb']) { print '[終日]'; }; ?>
					</span>
					<?php print htmlspecialchars_decode($v2['link_detail']); ?>
				</p>
			<?php // include("detail_pop.php"); //.lcm_focus内にあるとフォーカス時にdisplay:none;も読み上げてしまうので下に移動...ガッハッハ ?>
			<?php } ?>
			</div>
		</div>
		<?php } ?>
	</td>
	<?php if ($v['week'] == 0) { print '</tr>'; } ?>
<?php } ?>
</table>
</div><!-- /.field_wrapper -->
<?php foreach($schedule_data as $v) { 
	if(isset($v['day'])){
		foreach ($v['data'] as $v2) {
			$detail_pop_data = $v2;
			include("detail_pop.php");
		}
	}
} ?>

