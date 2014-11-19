<h1>Forbidden</h1>

<p>You don't have permission to access here.</p>


<?php
	if(\Auth::check())
	{
		$home = \Config::get('no_home') ? \Uri::create('admin/admin/dashboard/') : \Uri::base();
	}else{
		$home = \Config::get('no_home') ? \Uri::create('user/auth/login') : \Uri::base();
	}
?>
<p><a href="<?php echo $home ?>">ホームへ</a></p>

