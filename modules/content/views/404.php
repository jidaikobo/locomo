<h1>Not Found.</h1>

<p>The requested URL <code><?php echo \Input::protocol().'://'.\Input::server('HTTP_HOST').\Input::server('REQUEST_URI') ?></code> was not found on this server.</p>
<?php
	if(\Auth::check())
	{
		$home = \Config::get('no_home') ? \Uri::create('admin/admin/dashboard/') : \Uri::base();
	}else{
		$home = \Config::get('no_home') ? \Uri::create('user/auth/login') : \Uri::base();
	}
?>
<p><a href="<?php echo $home ?>">ホームへ</a></p>

