<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<?php
		if (\Uri::current() == \Uri::base()):
			$title = \Config::get('site_title').' - '.$title;
		else:
			$title = $title.' - '.\Config::get('site_title');
		endif;
	?>
	<title><?php echo $title; ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=0.5,user-scalable=yes,initial-scale=1.0" />

	<!--stylesheet-->
	<?php echo \Asset::css('base.css'); ?>
	<?php echo \Asset::css('core.css'); ?>
	<?php echo \Asset::css('layout.css'); ?>
	<?php echo \Asset::css('adminbar.css'); ?>
	<?php echo \Asset::css('code_profiler.css'); ?>
	<?php echo \Asset::css('../js/jquery-ui-1.10.4/css/smoothness/jquery-ui-1.10.4.custom.min.css'); ?>
	<?php echo \Asset::css('../js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.css'); ?>
	<?php echo \Asset::render('css'); ?>

	<!--JavaScript-->
	<?php echo \Asset::js('jquery-1.11.3.min.js'); ?>
	<?php echo \Asset::js('jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.min.js'); ?>
	<?php echo \Asset::js('jquery-ui-1.10.4/development-bundle/ui/i18n/jquery.ui.datepicker-ja.js'); ?>
	<?php echo \Asset::js('jquery.inc.js'); ?>
	<?php echo \Asset::js('jquery.exresize/jquery.exresize.0.1.0.js'); ?>

	<?php echo \Asset::js('jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js'); ?>
	<?php echo \Asset::js('jquery-ui-timepicker-addon/i18n/jquery-ui-timepicker-ja.js'); ?>
	<?php echo \Asset::js('jquery.timepicker/jquery.timepicker.js'); ?>
	<?php echo \Asset::js('jquery-ui-touch-punch/jquery.ui.touch-punch.min.js'); ?>
	<?php echo \Asset::js('jquery-autoKana/jquery.autoKana.js'); ?>
	<?php echo \Asset::js('tinymce/tinymce.min.js'); ?>
	<?php echo \Asset::render('js'); ?>
	<!--[if lt IE 9]>
	<?php echo \Asset::js('css3-mediaqueries-js-master/css3-mediaqueries.js'); ?>
	<![endif]-->

	<!-- favicon -->
	<link rel="shortcut icon" href="<?php echo Asset::get_file('system/favicon.ico', 'img') ?>">

</head>
<body class="<?php echo $body_class; echo ' '.\Fuel::$env; ?>" <?php echo $body_data ?>>
<?php
	echo (\Auth::check()) ? '<a href="#anchor_adminbar" class="skip show_if_focus">ツールバーに移動</a>' : '';
?>
	<div class="container clearfix">
<?php echo render('inc_messages'); ?>
		<div class="contents">
