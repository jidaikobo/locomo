<?php echo \View::forge(PKGPATH.'kontiki/views/inc_header.php'); ?>

<h2>New <span class='muted'>XXX</span></h2>

<?php echo render('_form'); ?>

<p><?php echo Html::anchor('xxx', 'Back'); ?></p>

<?php echo \View::forge(PKGPATH.'kontiki/views/inc_footer.php'); ?>
