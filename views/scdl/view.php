<?php
	$repeat_kbs = $model_name::get_repeat_kbs();
	$detail_kbs = $model_name::get_detail_kbs();
	$importance_kbs = $model_name::get_importance_kbs();
	$title_str = '' ;
	if(!$detail->private_kb):
		$title_str = '：';

		//外部表示(施設予約)
		if(\Request::active()->controller !== "\Controller_Scdl"):
			$title_str.= $detail['public_display']==2 ? '<span class="text_icon reserve public"><span class="skip">外部表示</span></span>' : '';
		endif;
		//詳細区分
		foreach($detail_kbs as $key => $value):
			if($detail->$key):
				$title_str .=  '<span class="text_icon schedule '.$key.'"><span class="skip">'.$value.'</span></span>';
			endif;
		endforeach;
		//繰り返し区分
		$title_str .=  $detail->repeat_kb != 0 ? '<span class="text_icon schedule repeat_kb_'.$detail->repeat_kb.'"><span class="skip"> '.$repeat_kbs[$detail->repeat_kb].'</span></span>' : '';
/*
		//重要度
		$importance_v = $model_name::value2index('title_importance_kb', html_entity_decode($detail->title_importance_kb));
		$title_str .= '<span class="icon"><img src="'.\Uri::base().'lcm_assets/img/system/mark_importance_'.$importance_v.'.png" alt="'.$importance_kbs[$importance_v].'"></span>';
*/
		$title_str .= $detail->title_text;
/*
		//区分
		$title_str .= $detail->title_kb!='標準' ? '('.$detail->title_kb.')' : '';
*/
	endif;
	$ymd_str = ( $year!='' && $mon!='' && $day!='' ) ?  intval($year).'年'.intval($mon).'月'.intval($day).'日 ' : '';
?>
<h1><?php echo $title ?>詳細<?php echo $title_str!='' ? $title_str : '：'.$detail->title_text ;?></h1>
<table class="tbl">
<?php
	// use model's form plain definition instead of raw-like html
	//echo $plain;
	$info = $model_name::make_target_day_info($detail);
?>
<tr>
	<th>予定日時</th>
	<td>
	<?php if($detail->repeat_kb == 0):
			echo $info['display_target_date'];
		elseif($detail->allday_kb):
			echo $ymd_str.'終日';
		else:
			echo $ymd_str.$info['display_period_time'];
		endif; ?>

	<?php
		if(\Request::active()->controller !== "Controller_Scdl" && ($detail->public_start_time!=0 || $detail->public_end_time!=0)):
			$start_time = $detail->public_start_time!=0 ? $detail->public_start_time : $detail->start_time;
			$start_time_hour   = date('G',strtotime('1974-12-25 '.$start_time)).'時';
			$start_time_minute = intval(date('i',strtotime('1974-12-25 '.$start_time)));
			$start_time_minute = $start_time_minute ? $start_time_minute.'分' : '';
			$end_time   = $detail->public_end_time!=0 ? $detail->public_end_time : $detail->end_time;
			$end_time_hour   = date('G',strtotime('1974-12-25 '.$end_time)).'時';
			$end_time_minute = intval(date('i',strtotime('1974-12-25 '.$end_time)));
			$end_time_minute = $end_time_minute ? $end_time_minute.'分' : '';

			echo '（実使用時間：';
			echo $start_time_hour.$start_time_minute;
			echo '<span class="sr_replace to"><span class="skip">から</span></span>';
			echo $end_time_hour.$end_time_minute;
			echo '）';
		endif;
		?>
	</td>
</tr>
<?php if($detail->repeat_kb != 0): ?>
<tr>
	<th>期間</th>
	<td>
		<?php echo $info['display_period_day'].' （'.$info['display_repeat_kb'].'）'; ?>
	</td>
</tr>
<?php endif; ?>
<?php /* ?>
<tr>
<th>詳細設定</th>
<td>
	<?php if ($detail->provisional_kb) { print '<span class="add_bracket">仮登録</span>'; }; ?>
	<?php if ($detail->private_kb) { print '<span class="add_bracket">非公開</span>'; }; ?>
	<?php if ($detail->unspecified_kb) { print '<span class="add_bracket">時間指定なし</span>'; }; ?>
	<?php if ($detail->allday_kb) { print '<span class="add_bracket">終日</span>'; }; ?>
</td>
</tr>
<?php */ ?>
<?php if(!$detail->private_kb): ?>
<tr>
	<th>メッセージ</th>
	<td><?php echo preg_replace("/(\r\n|\r|\n)/", "<br />", $detail->message); ?></td>
</tr>

<?php endif; ?>

<?php if (count($detail->user) && !$detail->private_kb) { ?>
<tr>
<th>メンバー</th>
<td>
	<?php $members = [];
	foreach ($detail->user as $row) {
		$members[] .= $row['display_name'];
	}
	echo '<span style="inline-block">'.implode(',</span> <span  style="display: inline-block">', $members).'</span>';
	?>
</td>
</tr>
<?php } ?>

<?php if (count($detail->building) && !$detail->private_kb) { ?>
<tr>
<th>対象施設</th>
<td>
	<?php $buildings = [];
	ksort($detail->building);
	foreach ($detail->building as $row) {
		$buildings[] .= $row['item_name'];
	}
	echo '<span style="inline-block">'.implode(',</span> <span  style="display: inline-block">', $buildings).'</span>';
	?>
</td>
</tr>
<?php } ?>

<?php if($locomo['controller']['name'] === "\Reserve\Controller_Reserve" && $detail->purpose_kb && !$detail->private_kb): //施設選択の時 ?>
<tr>
	<th class="min">施設使用目的</th>
	<td><?php echo $detail->purpose_kb; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->purpose_text && !$detail->private_kb): ?>
<tr>
	<th>施設使用目的テキスト</th>
	<td><?php echo $detail->purpose_text; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->user_num && !$detail->private_kb): ?>
<tr>
	<th>施設利用人数</th>
	<td><?php echo $detail->user_num; ?>人</td>
</tr>

<?php endif; ?>

<?php if (count($schedule_members_me)) { ?>
<tr>
<th>出席予定</th>
<td>
	<?php foreach ($schedule_attend_members as $row) { ?>
		<p><?php print $row->user->username; ?>:<?php print $row->attend->item_name; ?></p>
	<?php } ?>
</td>
</tr>
<?php } ?>

<?php if($detail->user_id): ?>
<tr>
	<th>作成者</th>
	<td><?php echo @$detail->create_user->display_name; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->updated_at): ?>
<tr>
	<th>更新日時</th>
	<td><?php echo date('Y年n月j日 G時i分', strtotime($detail->updated_at)); ?></td>
</tr>
<?php endif; ?>

<?php if($detail->updater_id): ?>
<tr>
	<th>更新者</th>
	<td><?php echo \Model_Usr::get_display_name($detail->updater_id); ?></td>
</tr>
<?php endif; ?>



</table>
<?php include("inc_legend.php"); //カレンダ凡例 ?>

<?php /* ?>
	<div class="legend calendar" aria-hidden=true>
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
<?php */ ?>
