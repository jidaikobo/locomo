<?php echo $include_tpl('inc_header.php'); ?>

<h2>Editing <span class='muted'>User</span></h2>
<br>

<?php echo render('_form'); ?>
<p>
	<?php echo Html::anchor('user/view/'.$item->id, 'View'); ?> |
	<?php echo Html::anchor('user', 'Back'); ?></p>

<?php echo $include_tpl('inc_footer.php'); ?>