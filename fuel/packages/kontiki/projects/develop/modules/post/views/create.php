<?php echo $include_tpl('inc_admin_header.php'); ?>

<h2>New <span class='muted'>Post</span></h2>

<?php echo render('_form'); ?>

<p><?php echo Html::anchor('post', 'Back'); ?></p>

<?php echo $include_tpl('inc_admin_footer.php'); ?>
