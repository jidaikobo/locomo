<h1><?php echo $title; ?></h1>
<?php if ($items): ?>
<table class="tbl2">
	<thead>
		<tr>
			<th><?php echo $field ?></th>
			<th>操作</th>
			<th>編集者</th>
			<th>更新日時</th>
			<th>コメント</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr>
			<td style="white-space: nowrap;"><a href="<?php echo $base_url.'view_revision/'.$item->id ?>"><?php echo \Arr::get($item->data, $subject, '名称未確定'); ?></a></td>
			<td><?php echo $item->operation; ?></td>
			<td><?php echo $item->modifier_name; ?></td>
			<td style="white-space: nowrap;"><?php echo $item->created_at; ?></td>
			<td><?php echo $item->comment; ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>編集履歴が存在しません。</p>
<?php endif; ?>

