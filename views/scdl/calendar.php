<?php 
	$repeat_kbs = $model_name::get_repeat_kbs();
	$detail_kbs = $model_name::get_detail_kbs();
	$importance_kbs = $model_name::get_importance_kbs();
?>
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
<?php foreach($schedule_data as $v) { ?>
	<?php if ($v['week'] == 1) { print '<tr>'; } ?>
	<td class="week<?php print $v['week']; print isset($v['day']) ? '' : ' empty'; ?>">
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
				<span class="date_str"><?php print $v['day'] < 10 ? '&nbsp;'.$v['day'] : $v['day']; ?>日</span>
				<span class="skip"><?php print $week_name[$v['week']] . '曜日'; ?> <?php if (count($v['data']) > 0) { print count($v['data']) . '件の登録';} else { print '登録なし'; } ?></span>
			</a>
			<a href="<?php echo \Uri::create($kind_name . "/create?ymd=" . htmlspecialchars(sprintf("%04d-%02d-%02d", $year, $mon, $v['day']))); ?>" class="add_new" title="新規追加"><span class="skip">新規追加</span></a>
			<?php /*?><div>
			<?php print count($v['data']); ?>件
			</div><?php */ ?>
			<div class="events">
			<?php foreach ($v['data'] as $v2) {
				$detail_pop_data = $v2;

				$detail_pop_array[] = $v2;

				?>
				
				<p class="lcm_tooltip_parent" data-jslcm-tooltip-id="pop<?php echo $detail_pop_data->scdlid.$detail_pop_data->target_year.$detail_pop_data->target_mon.$detail_pop_data->target_day ?>">
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
			<?php // include("detail_pop.php"); //.lcm_focus内にあるとフォーカス時にdisplay:none;も読み上げてしまうので下に移動...ガッハッハ? ?>
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
</div><!-- /.field_wrapper -->
<?php foreach($detail_pop_array as $v) { 
	$detail_pop_data = $v;
	include("detail_pop.php");
} ?>

