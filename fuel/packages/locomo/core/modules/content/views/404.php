
<p>ページが見つかりませんでした。</p>

<?php $home = \Config::get('no_home') ? \Uri::create('user/auth/login') : \Uri::base(); ?>
<p><a href="<?php echo $home ?>">ホームへ</a></p>

