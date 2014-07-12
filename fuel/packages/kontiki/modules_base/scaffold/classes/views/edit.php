<?php echo $include_tpl('inc_admin_header.php'); ?>

<h2>Editing <span class='muted'>XXX</span></h2>

<?php echo render('_form'); ?>

<p>
	<?php echo Html::anchor('xxx/view/'.$item->id, 'View'); ?> |
	<?php echo Html::anchor('xxx', 'Back'); ?>
</p>

<?php echo $include_tpl('inc_admin_footer.php'); ?>
