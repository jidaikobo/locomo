<?php echo render('inc_header'); ?>

<h2>項目一覧 (<?php echo $hit ?>)</h2>
<?php if ($items): ?>
<table class="tbl">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th>表題</th>
			<th>本文</th>
			<th>真偽値</th>
			<th>作成日時</th>
			<th>更新日時</th>
			<th>有効期日</th>
			<th>削除日</th>
			<th>可視属性</th>

		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><?php echo $item->title; ?></td>
	<td><?php echo $item->body; ?></td>
	<td><?php echo $item->is_bool ? 'Yes' : 'No' ; ?></td>
	<td><?php echo $item->created_at; ?></td>
	<td><?php echo $item->updated_at; ?></td>
	<td><?php echo $item->expired_at; ?></td>
	<td><?php echo $item->deleted_at; ?></td>
	<td><?php echo $item->is_visible ? 'Yes' : 'No' ; ?></td>

		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo $pagination; ?>

<?php else: ?>
<p>testが存在しません。</p>

<?php endif; ?>

<?php echo render('inc_footer'); ?>
