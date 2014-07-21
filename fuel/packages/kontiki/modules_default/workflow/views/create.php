<?php echo $include_tpl('inc_header.php'); ?>

<h2>新規ワークフロー</h2>

<?php echo render('_form'); ?>

<p><?php echo Html::anchor('workflow/index_admin', 'Back'); ?></p>

<?php echo $include_tpl('inc_footer.php'); ?>
