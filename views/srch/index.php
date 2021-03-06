<?php echo $search_form; ?>

<?php if ($items): ?>

<!--.index_toolbar-->
<div class="index_toolbar clearfix">
<?php echo \Pagination::create_links(); ?>
</div><!-- /.index_toolbar -->

<dl class="search_results">
<?php
foreach ($items as $item):

	// 付近の文字列を表示
	$all = mb_convert_kana(\Input::get('all'), "asKV");
	$alls = explode(' ', $all);
	$pos = mb_strpos($item->search, $alls[0]) - mb_strlen($alls[0]);
	$text = mb_substr($item->search, $pos, 160).'...';
	foreach($alls as $v):
		$text = str_replace($v, '<strong>'.$v.'</strong>',$text);
	endforeach;

?>
<dt><a href="<?php echo \Uri::create($item->path.'/'.$item->pid); ?>"><?php echo $item->title; ?></a></dt>
	<dd><?php echo $text ?></dd>
<?php endforeach; ?>
</dl>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>検索結果が存在しません。</p>
<?php endif; ?>
