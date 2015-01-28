

<?php if ($items): ?>

<script>

$(window).on('load',function(){
var parentbox = $('.lcm_flr_slideblock');

sn = setTimeout( function() { requestAnimationFrame(slides)}, 3000 );

var slides = function(){
	clearTimeout(sn);
	var nowpict = parentbox.find('div.current');
	var nextpict = nowpict.next('div')[0] ? nowpict.next('div') : parentbox.find('div.first');
	nowpict.fadeTo('slow','0').removeClass('current');
	nextpict.fadeTo('slow',1).addClass('current');
	sn = setTimeout( function() { requestAnimationFrame(slides)}, 3000 );
}

window.requestAnimationFrame = (function(){
	return window.requestAnimationFrame ||
	window.webkitRequestAnimationFrame ||
	window.mozRequestAnimationFrame ||
	window.oRequestAnimationFrame ||
	window.msRequestAnimationFrame ||
	function(callback, element){
	window.setTimeout(callback, 1000 / 60);
};
})();

});

</script>

<div class="lcm_flr_slideblock" style="
	position:relative;
	width: 100%;
	height: 100%;
	background-color: #fff;
">
	<?php
	$n = 1;
	foreach ($items as $item):
		if (\Controller_Flr::check_auth($item->path)):
		$url = \Uri::create('flr/dl/?p='.\Model_Flr::enc_url($item->path, true));
		$url = \Inflector::get_root_relative_path($url);
		?>
		<div
			class="
<?php if ($n == 1): ?>
			current first
<?php endif; ?>"
			style="
			position:absolute;
			opacity:0;
			display:none;
<?php if ($n == 1): ?>
			opacity:1;
			display:block;
<?php endif; ?>
			width: 100%;
			height: 100%;
			max-width: 100%;
			max-height: 100%;
			background-image: url('<?php echo $url; ?>');
			background-size: cover;
		">
		</div>
<?php
		$n++;
		endif;
	endforeach;
?>
</div>
<?php else: ?>
<p>画像が存在しません。<a href="<?php echo \Uri::create('/flr/index_files/') ?>">ファイラ</a>で画像をアップして、「ダッシュボードに表示」をチェックしてください。</p>
<?php endif; ?>
