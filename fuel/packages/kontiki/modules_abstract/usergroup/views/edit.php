<?php echo \View::forge('inc_header'); ?>

<h2>Editing <span class='muted'>Usergroup</span></h2>
<br>

<?php echo render('_form'); ?>
<p>
	<?php echo Html::anchor('usergroup/view/'.$item->id, 'View'); ?> |
	<?php echo Html::anchor('usergroup', 'Back'); ?></p>

<?php echo \View::forge('inc_footer');
