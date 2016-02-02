<table class="tbl">
<?php
	// use model's form plain definition instead of raw-like html
	//echo $plain;
?>
<?php if ($item->name): ?>
<tr>
	<th>グループ名</th>
	<td><?php echo $item->name; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->summary): ?>
<tr>
	<th></th>
	<td><?php echo $item->summary; ?></td>
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
<?php if ($item->is_available): ?>
<tr>
	<th></th>
	<td><?php echo $item->is_available ? 'Yes' : 'No'; ?></td>
</tr>

<?php endif; ?>

</table>
