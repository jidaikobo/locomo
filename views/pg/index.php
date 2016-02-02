<h1>項目一覧<?php echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : ''; ?></h1>

<?php if ($items): ?>
<table class="tbl">
	<thead>
		<tr>
			<th class="ar min"><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th>表題</th>

		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><?php echo $item->title; ?></td>

		</tr>
<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<th class="ar min"><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th>表題</th>

		</tr>
	</tfoot>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>pgが存在しません。</p>

<?php endif; ?>

