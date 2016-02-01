<table class="tbl">
<?php
	// use model's form plain definition instead of raw-like html
	//echo $plain;
?>
<?php if ($item->title): ?>
<tr>
	<th>表題</th>
	<td><?php echo $item->title; ?></td>
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
<?php if ($item->summary): ?>
<tr>
	<th></th>
	<td><?php echo $item->summary; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->content): ?>
<tr>
	<th></th>
	<td><?php echo $item->content; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->lat): ?>
<tr>
	<th></th>
	<td><?php echo $item->lat; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->lng): ?>
<tr>
	<th></th>
	<td><?php echo $item->lng; ?></td>
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
<?php if ($item->is_sticky): ?>
<tr>
	<th></th>
	<td><?php echo $item->is_sticky ? 'Yes' : 'No'; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->is_available): ?>
<tr>
	<th></th>
	<td><?php echo $item->is_available ? 'Yes' : 'No'; ?></td>
</tr>

<?php endif; ?>

</table>
