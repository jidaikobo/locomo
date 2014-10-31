<?php echo render('inc_header'); ?>

<h1>Viewing #<?php echo $item->id; ?></h1>

<table class="tbl">
<?php if($item->title): ?>
<tr>
	<th>表題</th>
	<td><?php echo $item->title; ?></td>
</tr>

<?php endif; ?>
<?php if($item->body): ?>
<tr>
	<th>本文</th>
	<td><?php echo $item->body; ?></td>
</tr>

<?php endif; ?>
<?php if($item->is_bool): ?>
<tr>
	<th>真偽値</th>
	<td><?php echo $item->is_bool ? 'Yes' : 'No' ; ?></td>
</tr>

<?php endif; ?>
<?php if($item->created_at): ?>
<tr>
	<th>作成日時</th>
	<td><?php echo $item->created_at; ?></td>
</tr>

<?php endif; ?>
<?php if($item->updated_at): ?>
<tr>
	<th>更新日時</th>
	<td><?php echo $item->updated_at; ?></td>
</tr>

<?php endif; ?>
<?php if($item->expired_at): ?>
<tr>
	<th>有効期日</th>
	<td><?php echo $item->expired_at; ?></td>
</tr>

<?php endif; ?>
<?php if($item->deleted_at): ?>
<tr>
	<th>削除日</th>
	<td><?php echo $item->deleted_at; ?></td>
</tr>

<?php endif; ?>

</table>

<?php echo render('inc_footer'); ?>
