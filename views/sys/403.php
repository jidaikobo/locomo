<h1>Forbidden</h1>

<p>You don't have permission to access here.</p>

<?php
	if (\Auth::check()):
		$home = \Config::get('no_home') ? \Uri::create('sys/dashboard/') : \Uri::base();
	else:
		$home = \Config::get('no_home') ? \Uri::create('auth/login') : \Uri::base();
	endif;
?>
<p><a href="<?php echo $home ?>">ホームへ</a></p>

