
<h2>項目一覧<?php echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : ''; ?></h2>
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
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>xxxが存在しません。</p>

<?php endif; ?>

