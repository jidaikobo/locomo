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
	<?php echo \Asset::css('../js/jquery-ui-1.10.4/css/smoothness/jquery-ui-1.10.4.custom.min.css'); ?>
	<?php echo \Asset::css('../js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.css'); ?>
	<?php echo \Asset::css('../js/jquery.timepicker/jquery.timepicker.css'); ?>
	<?php echo \Asset::css('../js/tinymce/css/tinymce.css'); ?>
	<?php echo \Asset::render('css'); ?>


	<!--JavaScript-->
	<?php echo \Asset::js('jquery-1.11.3.min.js'); ?>
	<?php echo \Asset::js('jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.min.js'); ?>
	<?php echo \Asset::js('jquery-ui-1.10.4/development-bundle/ui/i18n/jquery.ui.datepicker-ja.js'); ?>
	<?php echo \Asset::js('jquery.inc.js'); ?>
	<?php echo \Asset::js('jquery.lcm.focus.js'); ?>
	<?php echo \Asset::js('jquery-ui.dragresize.js'); ?>
	<?php echo \Asset::js('jquery.lcm.checkall.js'); ?>
	<?php echo \Asset::js('jquery.lcm.multipleselect.js'); ?>
	<?php echo \Asset::js('jquery-ui.inc.date.timepicker.js'); ?>
	<?php echo \Asset::js('jquery-ui.inc.tooltip.js'); ?>
	<?php echo \Asset::js('jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js'); ?>
	<?php echo \Asset::js('jquery.timepicker/jquery.timepicker.js'); ?>
	<?php echo \Asset::js('jquery-ui-timepicker-addon/i18n/jquery-ui-timepicker-ja.js'); ?>
	<?php echo \Asset::js('jquery-ui-touch-punch/jquery.ui.touch-punch.min.js'); ?>
	<?php echo \Asset::js('tinymce/js/tinymce.min.js'); ?>
	<?php echo \Asset::js('inc.tinymce.js'); ?>
	<?php echo \Asset::js('jquery-autoKana/jquery.autoKana.js'); ?>
	<?php echo \Asset::render('js'); ?>
	<?php echo \Asset::js('admin.js'); ?>
	<!--[if lt IE 9]>
	<?php echo \Asset::js('css3-mediaqueries-js-master/css3-mediaqueries.js'); ?>
	<![endif]-->


	<!-- favicon -->
	<link rel="shortcut icon" href="<?php echo Asset::get_file('system/favicon.ico', 'img') ?>">
	<script>
		<!--
		setTimeout(
			function(){var show_if_no_js = document.getElementsByClassName('show_if_no_js');
				for(var i = show_if_no_js.length-1 ; i >= 0; i--){
				show_if_no_js[i].style.display = 'none';
				}
			}
		,0);
		-->
	</script>
</head>
<body class="<?php echo $body_class ?>" <?php echo $body_data ?>>
	<div id="main_content" class="container clearfix" title="<?php echo $titlestr ?>ページ" tabindex="-1">
<?php 
	echo (\Auth::check()) ? '		<a href="#anchor_adminbar" class="skip show_if_focus">ツールバーに移動</a>' : '';
?>
<?php echo render('inc_messages'); ?>
		<div class="contents">
