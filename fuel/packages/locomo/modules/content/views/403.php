
<p>許可されていません。</p>

<?php
	if(\Auth::check())
	{
		$home = \Config::get('no_home') ? \Uri::create('admin/admin/dashboard/') : \Uri::base();
	}else{
		$home = \Config::get('no_home') ? \Uri::create('user/auth/login') : \Uri::base();
	}
?>
<p><a href="<?php echo $home ?>">ホームへ</a></p>

