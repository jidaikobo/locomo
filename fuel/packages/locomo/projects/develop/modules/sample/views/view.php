<?php echo render('inc_header'); ?>

<h2>Viewing #<?php echo $item->id; ?></h2>

<table class="tbl">
<tr>
	<th>name</th>
	<td><?php echo $item->name; ?></td>
</tr>

<tr>
	<th>belongsto_id</th>
	<td><?php echo $item->belongsto_id; ?></td>
</tr>

<tr>
	<th>created_at</th>
	<td><?php echo $item->created_at; ?></td>
</tr>

<tr>
	<th>expired_at</th>
	<td><?php echo $item->expired_at; ?></td>
</tr>

<tr>
	<th>deleted_at</th>
	<td><?php echo $item->deleted_at; ?></td>
</tr>


</table>

<?php echo render('inc_footer'); ?>
