<?php echo render('inc_header'); ?>

<p>ページが見つかりませんでした。</p>

<?php $home = \Config::get('use_login_as_top') ? \Uri::create('user/login') : \Uri::base(); ?>
<p><a href="<?php echo $home ?>">ホームへ</a></p>

<?php echo render('inc_footer'); ?>
