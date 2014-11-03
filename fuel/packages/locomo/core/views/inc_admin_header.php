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
</head>
<body class="<?php echo $body_class ;?>">
	<div class="container">

		<div class="contents">
			<h1 class="page_title skip"><?php echo $title; ?></h1>
<?php if (Session::get_flash('success')): ?>
			<div class="alert alert_success">
				<!-- <strong>Success</strong> -->
				<p>
				<?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
				</p>
			</div>
<?php endif; ?>
<?php if (Session::get_flash('error')): ?>
			<div class="alert alert_error">
				<strong class="skip">エラー</strong>
				<p>
				<?php echo implode('</p><p>', e((array) Session::get_flash('error'))); ?>
				</p>
			</div>
<?php endif; ?>
