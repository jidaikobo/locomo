<?php 
	$repeat_kbs = array('0' => 'なし', '1' => '毎日', '2' => '毎日(土日除く)', '3' => '毎週', '4' => '毎月', '6' => '毎月(曜日指定)', '5' => '毎年');
	$detail_kbs = array('provisional_kb' => '仮登録', 'unspecified_kb' => '時間指定なし', 'allday_kb' => '終日');
	$importance_kbs = array('重要度 高', '重要度 中', '重要度 低');
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
?>
	<span class="display_inline_block"><span class="icon mark_private"><img src="<?php echo \Uri::base() ?>lcm_assets/img/system/mark_private.png" alt="非公開"></span>非公開</span>
	 </div><!-- /.legend.calendar -->
<?php foreach($schedule_data as $v) {?>
	<?php if (isset($v['day'])) { ?>
		<?php foreach ($v['data'] as $v2) {
				$detail_pop_data = $v2;
				include("detail_pop.php");
		}?>
	<?php } ?>
<?php } ?>


