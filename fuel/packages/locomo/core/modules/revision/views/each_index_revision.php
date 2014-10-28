<?php echo render('inc_header'); ?>

<?php if ($items): ?>
<table class="tbl2">
	<thead>
		<tr>
			<th>最新表題</th>
			<th>操作</th>
			<th>編集者</th>
			<th>更新日時</th>
			<th>コメント</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr>
			<td style="white-space: nowrap;"><a href="<?php echo \Uri::base().$controller.'/view_revision/'.$item->id ?>"><?php echo $item->data->$subject; ?></a></td>
			<td><?php echo $item->operation; ?></td>
			<td><?php echo $item->modifier_id; ?></td>
			<td style="white-space: nowrap;"><?php echo $item->created_at; ?></td>
			<td><?php echo $item->comment; ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>編集履歴が存在しません。</p>
<?php endif; ?>

<?php echo render('inc_footer'); ?>
