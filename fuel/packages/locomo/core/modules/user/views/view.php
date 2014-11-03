<?php echo render('inc_header'); ?>

<h2>Viewing <span class='muted'>#<?php echo $item->id; ?></span></h2>

<table class="tbl">
<tr>
	<th>User name:</th>
	<td><?php echo $item->user_name; ?></td>
</tr>
<tr>
	<th>Email:</th>
	<td><?php echo $item->email; ?></td>
</tr>
<tr>
	<th>Last login:</th>
	<td><?php echo $item->last_login_at; ?></td>
</tr>
</table>

<?php echo render('inc_footer'); ?>
