
<h1>Viewing #<?php echo $item->id; ?></h1>

<table class="tbl">
<?php if($item->title): ?>
<tr>
	<th>表題</th>
	<td><?php echo $item->title; ?></td>
</tr>

<?php endif; ?>
<?php if($item->controller): ?>
<tr>
	<th>コントローラ</th>
	<td><?php echo $item->controller; ?></td>
</tr>

<?php endif; ?>
<?php if($item->body): ?>
<tr>
	<th>本文</th>
	<td><?php echo $item->body; ?></td>
</tr>

<?php endif; ?>
<?php if($item->updated_at): ?>
<tr>
	<th>更新日時</th>
	<td><?php echo $item->updated_at; ?></td>
</tr>

<?php endif; ?>
<?php if($item->deleted_at): ?>
<tr>
	<th>削除日</th>
	<td><?php echo $item->deleted_at; ?></td>
</tr>

<?php endif; ?>
<?php if($item->order): ?>
<tr>
	<th></th>
	<td><?php echo $item->order; ?></td>
</tr>

<?php endif; ?>

</table>

