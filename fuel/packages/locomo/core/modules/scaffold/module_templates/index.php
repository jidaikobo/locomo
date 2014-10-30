<?php echo render('inc_header'); ?>

<h2>項目一覧 (<?php echo $hit ?>)</h2>
<?php if ($items): ?>
<table class="tbl">
	<thead>
		<tr>
###THEAD###
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
###TBODY###
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo $pagination; ?>

<?php else: ?>
<p>xxxが存在しません。</p>

<?php endif; ?>

<?php echo render('inc_footer'); ?>
