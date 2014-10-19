<?php echo render('inc_header'); ?>

<h2>Viewing #<?php echo $item->id; ?></h2>

<table class="tbl">
<tr>
	<th>title</th>
	<td><?php echo $item->title; ?></td>
</tr>

<tr>
	<th>body</th>
	<td><?php echo $item->body; ?></td>
</tr>

<tr>
	<th>is_bool</th>
	<td><?php echo $item->is_bool ? 'Yes' : 'No' ; ?></td>
</tr>

<tr>
	<th>status</th>
	<td><?php echo $item->status; ?></td>
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
