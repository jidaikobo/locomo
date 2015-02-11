<!DOCTYPE html>
<?php if (1): ?><!--
++
||==+ +-+
 +o-o o-o
Locomo - Accessible Web System Package for FuelPHP version.<?php echo LOCOMOVERSION ?>

--><?php endif; ?>

<html>
<head>
	<meta charset="utf-8">
	<?php
			$titlestr = $title;
		if (\Uri::current() == \Uri::base()):
			$title = \Config::get('site_title').' - '.$title;
		else:
			$title = $title.' - '.\Config::get('site_title');
		endif;
	?>
	<title><?php echo $title; ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<!--stylesheet-->
	<?php echo \Asset::css('base.css'); ?>
	<?php echo \Asset::css('core.css'); ?>
	<?php echo \Asset::css('admin.css'); ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.min.css">
	<?php echo \Asset::css('../js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.css'); ?>
	<?php echo \Asset::render('css'); ?>


	<!--JavaScript-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<?php echo \Asset::js('jquery.inc.js'); ?>
	<?php echo \Asset::js('jquery.exresize/jquery.exresize.0.1.0.js'); ?>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.js"></script>
	<?php echo \Asset::js('jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js'); ?>
	<?php echo \Asset::js('jquery-ui-timepicker-addon/i18n/jquery-ui-timepicker-ja.js'); ?>
	<?php echo \Asset::js('jquery-ui-touch-punch/jquery.ui.touch-punch.min.js'); ?>
	<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
	<?php echo \Asset::render('js'); ?>
	<!--[if lt IE 9]>
	<script src="//css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->

	<!-- favicon -->
	<link rel="shortcut icon" href="<?php echo Asset::get_file('system/favicon.ico', 'img') ?>">

</head>
<body class="<?php echo $body_class ?>" <?php echo $body_data ?>>
	<div id="main_content" class="container" title="<?php echo $titlestr ?>ページ" tabindex="-1">
<?php 
	echo (\Auth::check()) ? '		<a href="#anchor_adminbar" class="skip show_if_focus">ツールバーに移動</a>' : '';
?>
<?php echo render('inc_messages'); ?>
		<div class="contents">
