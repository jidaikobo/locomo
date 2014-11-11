
<p>許可されていません。</p>

<?php $home = \Config::get('use_login_as_top') ? \Uri::create('user/auth/login') : \Uri::base(); ?>
<p><a href="<?php echo $home ?>">ホームへ</a></p>

