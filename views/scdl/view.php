calendar【<?php print $detail->title_text; ?>】


<table class="tbl">
<?php
	// use model's form plain definition instead of raw-like html
	//echo $plain;
?>

<?php if($detail->start_date): ?>
<tr>
	<th>開始日時</th>
	<td>
	<?php 
		if ($detail->repeat_kb == 0) {
			echo $detail->start_date . " " . $detail->start_time . "〜" . $detail->end_date . " " . $detail->end_time;
		} else {
			echo $year . "/" . $mon . "/" . $day . " " . $detail->start_time . "〜" . $detail->end_time;
			if ($detail->week_kb != "" && $detail->repeat_kb == 2) {
				echo "(";
				$week = array('日', '月', '火', '水', '木', '金', '土');
				if ($detail->week_index) {
					echo "第" . $detail->week_index;
				} else {
					echo "毎週";
				}
				echo $week[$detail->week_kb] . "曜日)";
			}
		}?></td>
</tr>

<?php endif; ?>

<?php if($detail->title_text && !$detail->private_kb): ?>
<tr>
	<th>タイトル</th>
	<td><?php echo $detail->title_text; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->title_importance_kb && !$detail->private_kb): ?>
<tr>
	<th>タイトル（重要度）</th>
	<td><?php echo $detail->title_importance_kb; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->title_kb && !$detail->private_kb): ?>
<tr>
	<th>タイトル（区分）</th>
	<td><?php echo $detail->title_kb; ?></td>
</tr>

<?php endif; ?>

<tr>
<th>詳細設定</th>
<td>
	<span class="icon_small">
	<?php if ($detail->provisional_kb) { print '[仮登録]'; }; ?>
	<?php if ($detail->unspecified_kb) { print '[時間指定なし]'; }; ?>
	<?php if ($detail->allday_kb) { print '[終日]'; }; ?>
		</span>
</td>
</tr>

<?php if($detail->message && !$detail->private_kb): ?>
<tr>
	<th>メッセージ</th>
	<td><?php echo $detail->message; ?></td>
</tr>

<?php endif; ?>

<?php if($detail->purpose_kb && !$detail->private_kb): ?>
<tr>
	<th>施設使用目的区分</th>
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
	<td><?php echo $detail->user_num; ?></td>
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
	<td><?php echo @$detail->create_user->username; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->updated_at): ?>
<tr>
	<th>更新日時</th>
	<td><?php echo $detail->updated_at; ?></td>
</tr>

<?php endif; ?>

</table>




