<?php echo $include_tpl('inc_header.php'); ?>

<h2>New <span class='muted'>User</span></h2>
<br>

<?php echo render('_form'); ?>


<p><?php echo Html::anchor('user', 'Back'); ?></p>

<?php echo $include_tpl('inc_footer.php'); ?>
