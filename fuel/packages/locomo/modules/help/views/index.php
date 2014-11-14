
<h2>項目一覧<?php echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : ''; ?></h2>
<?php if ($items): ?>
<table class="tbl">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th>表題</th>
			<th>コントローラ</th>
			<th>本文</th>
			<th>更新日時</th>
			<th>削除日</th>

		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><?php echo $item->title; ?></td>
	<td><?php echo $item->controller; ?></td>
	<td><?php echo $item->body; ?></td>
	<td><?php echo $item->updated_at; ?></td>
	<td><?php echo $item->deleted_at; ?></td>

		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>helpが存在しません。</p>

<?php endif; ?>

