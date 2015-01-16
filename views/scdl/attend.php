calendar【<?php print $detail->title_text; ?>】


<table class="tbl">
<?php
	// use model's form plain definition instead of raw-like html
	//echo $plain;
?>
<?php if($detail->repeat_kb): ?>
<tr>
	<th>繰り返し区分</th>
	<td><?php echo $detail->repeat_kb; ?></td>
</tr>

<?php endif; ?>

<?php if($detail->start_date): ?>
<tr>
	<th>開始日時</th>
	<td><?php echo $detail->start_date; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->end_date): ?>
<tr>
	<th>終了日時</th>
	<td><?php echo $detail->end_date; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->week_kb): ?>
<tr>
	<th>繰り返し曜日</th>
	<td><?php echo $detail->week_kb; ?></td>
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
<?php if($detail->group_kb && !$detail->private_kb): ?>
<tr>
	<th>表示するグループフラグ</th>
	<td><?php echo $detail->group_kb; ?></td>
</tr>

<?php endif; ?>
<?php if($detail->group_detail && !$detail->private_kb): ?>
<tr>
	<th>グループ指定</th>
	<td><?php echo $detail->group_detail; ?></td>
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
<?php if (count($schedule_members_me)) { ?>
<form action="" method="post" >

<?php
foreach ($items as $key => $value) {
?>
	<input type="radio" name="attend_kb" value="<?php print $key; ?>" <?php if ($key==$attend['attend_kb']) { print "checked"; } ?>/><?php print $value; ?>	
<?php
}
?>
<input type="submit" value="決定" />
</form>
<?php } ?>


<input type="button" value="編集" onclick="location.href='<?php print Config::get('base_url'); ?>scdl/edit/<?php print $detail->id; ?>'" />

<input type="button" value="削除" onclick="location.href='<?php print Config::get('base_url'); ?>scdl/delete/<?php print $detail->id; ?>'" />

<input type="button" value="コピー" onclick="location.href='<?php print Config::get('base_url'); ?>scdl/edit/?from=<?php print $detail->id; ?>'" />
