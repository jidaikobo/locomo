
<p>ページが見つかりませんでした。</p>

<?php $home = \Config::get('use_login_as_top') ? \Uri::create('user/user/login') : \Uri::base(); ?>
<p><a href="<?php echo $home ?>">ホームへ</a></p>

