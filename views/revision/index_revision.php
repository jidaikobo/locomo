<?php echo  $search_form; ?>
<?php if ($items): ?>

<table class="tbl datatable">
	<thead>
		<tr>
			<th class="ctrl">ID</th>
			<th>最新表題</th>
			<th>操作</th>
			<th>編集者</th>
			<th>最新履歴日時</th>
			<th style="width:30%">コメント</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr>
			<th style="text-align:center;"><?php echo $item->pk_id ?></th>
			<th><div class="col_scrollable"><a href="<?php echo $base_url.'each_index_revision/'.$item->pk_id ?>"><?php echo $item->$subject; ?></a></div></th>
			<td><?php echo $item->operation; ?></td>
			<td><?php echo \Model_Usr::get_display_name($item->user_id); ?></td>
			<td><?php echo $item->created_at; ?></td>
			<td><div class="col_scrollable"><?php echo $item->comment; ?></div></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php echo $pagination ?>

<?php else: ?>
<p>編集履歴が存在しません。</p>
<?php endif; ?>

