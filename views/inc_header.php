<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo \Config::get('site_title').' - '.$title; ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<!--stylesheet-->
	<?php echo \Asset::css('base.css'); ?>
	<?php echo \Asset::css('core.css'); ?>
	<?php echo \Asset::css('layout.css'); ?>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.min.css">
	<link rel="stylesheet" href="<?php echo \Uri::base() ?>/sys/fetch_view/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.css">
	<?php echo \Asset::render('css'); ?>

	<!--JavaScript-->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<?php echo \Asset::js('jquery.inc.js'); ?>
	<script src="<?php echo \Uri::base() ?>/sys/fetch_view/js/jquery.exresize/jquery.exresize.0.1.0.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.js"></script>
	<script src="<?php echo \Uri::base() ?>/sys/fetch_view/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js"></script>
	<script src="<?php echo \Uri::base() ?>/sys/fetch_view/js/jquery-ui-timepicker-addon/i18n/jquery-ui-timepicker-ja.js"></script>
	<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
	<?php echo \Asset::render('js'); ?>
	<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->

</head>
<body class="<?php echo $body_class ?>" <?php echo $body_data ?>>
<?php 
	echo (\Auth::check()) ? '<a href="#anchor_adminbar" class="skip show_if_focus">ツールバーに移動</a>' : '';
?>
	<div class="container">
		<h1 class="page_title skip"><?php echo $title; ?></h1>
<?php if (Session::get_flash('message')): ?>
		<div id="alert_success" class="flash_alert alert_success" tabindex="0">
			<a id="anchor_alert_message" class="skip" tabindex="1" id="alert_message">インフォメーション:メッセージが次の行にあります</a>
			<p>
			<?php echo implode('</p><p>', e((array) Session::get_flash('message'))); ?>
			</p>
		</div>
<?php endif; ?>
<?php if (Session::get_flash('success')): ?>
		<div id="alert_success" class="flash_alert alert_success" tabindex="0">
			<a id="anchor_alert_success" class="skip" tabindex="1" id="alert_success">インフォメーション:メッセージが次の行にあります</a>
			<p>
			<?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
			</p>
		</div>
<?php endif; ?>
<?php if (Session::get_flash('error')): ?>
		<div id="alert_error" class="flash_alert alert_error" tabindex="0">
			<a id="anchor_alert_error" class="skip" tabindex="1">エラー:メッセージが次の行にあります</a>
<?php $i = 0;
foreach((array) Session::get_flash('error') as $id => $e):
if ($id === 0):
	echo "<p>$e</p>" ;
else:
	echo $i == 0 ? '<ul class="link">' : '';
	echo "<li><a href=\"#form_{$id}\" tabindex=\"1\">{$e}</a></li>";	$i ++;
endif;
endforeach;
echo $i!=0 ? '</ul>': '' ;
?>
			<?php // echo implode('</p><p>', e((array) Session::get_flash('error'))); ?>
		</div>
<?php endif; ?>
		<div class="contents">
