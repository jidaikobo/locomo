<table class="tbl">
<?php
	// use model's form plain definition instead of raw-like html
	//echo $plain;
?>
<?php if ($item->pg_id): ?>
<tr>
	<th></th>
	<td><?php echo $item->pg_id; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->name): ?>
<tr>
	<th>名前</th>
	<td><?php echo $item->name; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->path): ?>
<tr>
	<th></th>
	<td><?php echo $item->path; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->url): ?>
<tr>
	<th></th>
	<td><?php echo $item->url; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->alt): ?>
<tr>
	<th>alt</th>
	<td><?php echo $item->alt; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->created_at): ?>
<tr>
	<th></th>
	<td><?php echo $item->created_at; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->deleted_at): ?>
<tr>
	<th></th>
	<td><?php echo $item->deleted_at; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->expired_at): ?>
<tr>
	<th></th>
	<td><?php echo $item->expired_at; ?></td>
</tr>

<?php endif; ?>

</table>
