<h1>項目一覧<?php echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : ''; ?></h1>

<?php if ($items): ?>
<table class="tbl">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th>表題</th>
			<th>本文</th>
			<th>下書き</th>
			<th>カテゴリ</th>
			<th>作成日時</th>
			<th>更新日時</th>
			<th>有効期日</th>
			<th>削除日</th>

		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><?php echo $item->name; ?></td>
	<td><?php echo $item->contents; ?></td>
	<td><?php echo $item->is_draft ? 'Yes' : 'No'; ?></td>
	<td><?php echo $item->category_id; ?></td>
	<td><?php echo $item->created_at; ?></td>
	<td><?php echo $item->updated_at; ?></td>
	<td><?php echo $item->expired_at; ?></td>
	<td><?php echo $item->deleted_at; ?></td>

		</tr>
<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th>表題</th>
			<th>本文</th>
			<th>下書き</th>
			<th>カテゴリ</th>
			<th>作成日時</th>
			<th>更新日時</th>
			<th>有効期日</th>
			<th>削除日</th>

		</tr>
	</tfoot>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>msgbrdが存在しません。</p>

<?php endif; ?>

