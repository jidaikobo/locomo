<?php echo $include_tpl('inc_admin_header.php'); ?>

<h2>New <span class='muted'>Test</span></h2>

<?php echo render('_form'); ?>

<p><?php echo Html::anchor('test', 'Back'); ?></p>

<?php echo $include_tpl('inc_admin_footer.php'); ?>
