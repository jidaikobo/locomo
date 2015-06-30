<h1>Not Found.</h1>

<p>The requested URL <code><?php echo \Input::protocol().'://'.\Input::server('HTTP_HOST').\Input::server('REQUEST_URI') ?></code> was not found on this server.</p>

<?php
/*
	if (\Auth::check()):
		$home = \Config::get('no_home') ? \Uri::create('sys/dashboard/') : \Uri::base();
	else:
		$home = \Config::get('no_home') ? \Uri::create('auth/login') : \Uri::base();
	endif;
	echo '<p><a href="'.$home.'">home</a></p>';
*/
?>

