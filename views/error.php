<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>
	<meta name="robots" content="noindex, follow, noarchive">
	<style type="text/css">
	h1
	{
		font-size: 130%;
	}
	.skip
	{
		position: absolute !important;
		left: auto;
		clip: rect(1px 1px 1px 1px);
		clip: rect(1px, 1px, 1px, 1px);
		height: 1px;
		width: 1px;
		overflow: hidden;
	}
	</style>

	<!-- favicon -->
	<link rel="shortcut icon" href="<?php echo Asset::get_file('system/favicon.ico', 'img') ?>">
</head>
<body>
<?php echo render('inc_messages'); ?>
<?php echo $content ?>
</body>
</html>
