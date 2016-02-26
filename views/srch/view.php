<table class="tbl">
<?php
	// use model's form plain definition instead of raw-like html
	//echo $plain;
?>
<?php if ($item->controller): ?>
<tr>
	<th></th>
	<td><?php echo $item->controller; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->pid): ?>
<tr>
	<th></th>
	<td><?php echo $item->pid; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->url): ?>
<tr>
	<th></th>
	<td><?php echo $item->url; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->seacrh): ?>
<tr>
	<th></th>
	<td><?php echo $item->seacrh; ?></td>
</tr>

<?php endif; ?>

</table>
