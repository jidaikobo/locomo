<?php if ($items): ?>
<div style="
	width: 100%;
	height: 100%;
	background-color: #fff;
">
	<?php
	foreach ($items as $item):
		if (\Controller_Flr::check_auth($item->path)):
		$url = \Uri::create('flr/dl/?p='.\Model_Flr::enc_url($item->path, true));
		$url = \Inflector::get_root_relative_path($url);
		?>
		<div style="
			width: 100%;
			height: 100%;
			max-width: 100%;
			max-height: 100%;
			background-image: url('<?php echo $url; ?>');
			background-size: cover;
		">
		</div>
<?php
		endif;
	endforeach;
?>
</div>
<?php else: ?>
<p>画像が存在しません。<a href="<?php echo \Uri::create('/flr/index_files/') ?>">ファイラ</a>で画像をアップして、「ダッシュボードに表示」をチェックしてください。</p>
<?php endif; ?>
