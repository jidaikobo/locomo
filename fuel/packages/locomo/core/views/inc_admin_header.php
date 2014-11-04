<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo \Config::get('site_title').' - '.$title; ?></title>

	<!--stylesheet-->
	<?php echo \Asset::css('base.css'); ?>
	<?php echo \Asset::css('core.css'); ?>
	<?php echo \Asset::css('layout.css'); ?>
	<?php echo \Asset::css('admin.css'); ?>

	<!--JavaScript-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<?php echo \Asset::js('jquery.inc.js'); ?>
	<?php echo \Asset::js('jquery.exresize.0.1.0.js'); ?>
	
</head>
<body class="<?php echo $body_class ;?>">
	<div class="container">

		<div class="contents">
			<h1 class="page_title skip"><?php echo $title; ?></h1>
<?php if (Session::get_flash('success')): ?>
			<div id="alert_success" class="flash_alert alert_success" tabindex="1">
				<a class="skip" tabindex="1" id="alert_success">インフォメーション メッセージが次の行にあります</a>
				<p>
				<?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
				</p>
			</div>
<?php endif; ?>
<?php if (Session::get_flash('error')): ?>
			<div id="alert_error" class="flash_alert alert_error" tabindex="1">
				<a class="skip" tabindex="1" href="alert_error">エラー メッセージが次の行にあります</a>
				<p>
				<?php echo implode('</p><p>', e((array) Session::get_flash('error'))); ?>
				</p>
			</div>
<?php endif; ?>
