
<h1>Viewing #<?php echo $item->id; ?></h1>

<table class="tbl">
<?php if ($item->title): ?>
<tr>
	<th>表題</th>
	<td><?php echo $item->title; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->mod_or_ctrl): ?>
<tr>
	<th>コントローラ</th>
	<td><?php echo $item->mod_or_ctrl; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->body): ?>
<tr>
	<th>本文</th>
	<td><?php echo $item->body; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->updated_at): ?>
<tr>
	<th>更新日時</th>
	<td><?php echo $item->updated_at; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->deleted_at): ?>
<tr>
	<th>削除日</th>
	<td><?php echo $item->deleted_at; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->seq): ?>
<tr>
	<th></th>
	<td><?php echo $item->seq; ?></td>
</tr>

<?php endif; ?>

</table>

