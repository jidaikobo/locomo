<h1><?php print $year; ?>年<?php print (int)$mon; ?>月 カレンダ</h1>
<div class="select_display_type">
	<?php // print htmlspecialchars_decode($display_month); ?><!-- / -->
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
<?php
//if (\Request::main()->controller == 'Controller_Scdl'):
	include("calendar_narrow.php");
//else:
//	include(APPPATH."modules/reserve/views/reserve/calendar_narrow.php");
//endif;
?>
</div>

<?php 
	$repeat_kbs = array('0' => 'なし', '1' => '毎日', '2' => '毎日(土日除く)', '3' => '毎週', '4' => '毎月', '6' => '毎月(曜日指定)', '5' => '毎年');
	$detail_kbs = array('provisional_kb' => '仮登録', 'unspecified_kb' => '時間指定なし', 'allday_kb' => '終日');
	$importance_kbs = array('重要度 高', '重要度 中', '重要度 低');
?>
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
<?php $repeat_kbs = array('0' => 'なし', '1' => '毎日', '2' => '毎日(土日除く)', '3' => '毎週', '4' => '毎月', '6' => '毎月(曜日指定)', '5' => '毎年'); ?>
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
					<?php
//						echo $v2['title_importance_kb']  == '↑高' ? '↑高' : '';
						echo $v2['repeat_kb'] != 0 ? '<span class="text_icon schedule repeat_kb_'.$v2['repeat_kb'].'"><span class="skip"> '.$repeat_kbs[$v2['repeat_kb']].'</span></span>' : '';
						if ($v2['allday_kb']) { print '<span class="text_icon schedule allday_kb"><span class="skip">終日</span></span>'; };
						if ($v2['unspecified_kb']) { print '<span class="text_icon schedule unspecified_kb"><span class="skip">時間指定なし</span></span>'; };
						if ($v2['provisional_kb']) { print '<span class="text_icon schedule provisional_kb"><span class="skip">仮登録</span></span>'; };
						print htmlspecialchars_decode($v2['link_detail']);
					?>
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
<div class="legend calendar">
<?php
	foreach($repeat_kbs as $k => $v){
		echo $k != 0 ? '<span class="display_inline_block"><span class="text_icon schedule repeat_kb_'.$k.'"><span class="skip"> '.$v.'</span></span>'.$v.' </span>' : '';
	}
	foreach($detail_kbs as $k => $v){
		echo $k != 0 ? '<span class="display_inline_block"><span class="text_icon schedule repeat_kb_'.$k.'"><span class="skip"> '.$v.'</span></span>'.$v.' </span>' : '';
	}
	if(!\Request::is_hmvc()): //重要度
		foreach($importance_kbs as $k => $v){
			echo '<span class="display_inline_block"><span class="icon mark_importance"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$k.'.png" alt="'.$v.'"></span>'.$v.' </span>';
		}
	endif;
?>
	<span class="display_inline_block"><span class="icon mark_private"><img src="<?php echo \Uri::base() ?>lcm_assets/img/system/mark_private.png" alt="非公開"></span>非公開 </span>
</div><!-- /.legend.calendar -->
</div><!-- /.field_wrapper -->
<?php foreach($schedule_data as $v) { 
	if(isset($v['day'])){
		foreach ($v['data'] as $v2) {
			$detail_pop_data = $v2;
			include("detail_pop.php");
		}
	}
} ?>

