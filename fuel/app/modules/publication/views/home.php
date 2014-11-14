<?php echo render('inc_header'); ?>


<table>
	<?php foreach($menus as $a => $desc) : ?>
	<tr>
		<td><?php echo $a; ?></td>
		<td><?php echo $desc; ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php echo render('inc_footer'); ?>

