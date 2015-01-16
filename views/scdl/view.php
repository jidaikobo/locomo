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
<?php if($detail->user_id): ?>
<tr>
	<th>作成者</th>
	<td><?php echo $detail->user_id; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->created_at): ?>
<tr>
	<th>作成日時</th>
	<td><?php echo $detail->created_at; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->updated_at): ?>
<tr>
	<th>更新日時</th>
	<td><?php echo $detail->updated_at; ?></td>
</tr>

<?php endif; ?>

</table>

<!-- 自分自身ではなければ表示させない -->
<?php if (count($schedule_members_me) && $detail->attend_flg) { ?>

<input type="button" value="参加" onclick="location.href='<?php print Config::get('base_url'); ?>scdl/attend/<?php print $detail->id . sprintf("/%04d/%02d/%02d/", $year, $mon, $day);; ?>'" />

<?php } ?>

<input type="button" value="削除" onclick="location.href='<?php print Config::get('base_url'); ?>scdl/delete/<?php print $detail->id; ?>'" />

<?php if ($detail->provisional_kb) { ?>
<input type="button" value="本登録" onclick="if (confirm('本登録してもよろしいですか？')) {location.href='<?php print Config::get('base_url'); ?>scdl/regchange/<?php print $detail->id . sprintf("/%04d/%02d/%02d/", $year, $mon, $day);; ?>'}" />
<?php } ?>

<?php if ($detail->repeat_kb >= 1) { ?>
<input type="button" value="部分削除" onclick="if (confirm('部分削除してもよろしいですか？')) {location.href='<?php print Config::get('base_url'); ?>scdl/somedelete/<?php print $detail->id . sprintf("/%04d/%02d/%02d/", $year, $mon, $day); ?>'}" />
<?php } ?>
