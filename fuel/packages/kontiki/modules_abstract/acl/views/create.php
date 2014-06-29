<?php echo \View::forge('inc_header'); ?>

<h2>New <span class='muted'>User</span></h2>
<br>

<?php echo render('_form'); ?>


<p><?php echo Html::anchor('user', 'Back'); ?></p>

<?php echo \View::forge('inc_footer');
