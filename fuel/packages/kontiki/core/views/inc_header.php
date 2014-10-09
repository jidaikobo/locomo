<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $site_title.' - '.$title; ?></title>

	<!--stylesheet-->
	<link type="text/css" rel="stylesheet" href="<?php echo $include_asset('css/base.css'); ?>" />
	<link type="text/css" rel="stylesheet" href="<?php echo $include_asset('css/core.css'); ?>" />
	<link type="text/css" rel="stylesheet" href="<?php echo $include_asset('css/layout.css'); ?>" />

	<!--JavaScript-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="<?php echo $include_asset('js/jquery.inc.js'); ?>" type="text/javascript"></script>
</head>
<body>
	<div class="container">

		<div class="col-md-12">
			<h1><?php echo $title; ?></h1>
			<hr>
<?php if (Session::get_flash('success')): ?>
			<div class="alert alert-success">
				<!-- <strong>Success</strong> -->
				<p>
				<?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
				</p>
			</div>
<?php endif; ?>
<?php if (Session::get_flash('error')): ?>
			<div class="alert alert-error">
				<strong class="skip">エラー</strong>
				<p>
				<?php echo implode('</p><p>', e((array) Session::get_flash('error'))); ?>
				</p>
			</div>
<?php endif; ?>
		</div>
		<div class="col-md-12">
