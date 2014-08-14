<?php echo $include_tpl('inc_header.php'); ?>

<?php if ($items): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>更新日時</th>
			<th>コメント</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr>
			<td><a href="<?php echo \Uri::base().$item->controller.'/view_revision/'.$item->id ?>"><?php echo $item->created_at; ?></a></td>
			<td><?php echo $item->comment; ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>編集履歴が存在しません。</p>

<?php endif; ?>

<p><a href="<?php echo \Uri::base().$item->controller.'/edit/'.$item->controller_id ?>">編集画面に戻る</a></p>

<?php echo $include_tpl('inc_footer.php'); ?>
