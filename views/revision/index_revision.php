<?php echo  $search_form; ?>

<?php if ($items): ?>
<div class="index_toolbar clearfix">
<?php echo \Pagination::create_links(); ?>
</div>
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
		<tr tabindex="-1">
			<th style="text-align:center;"><?php echo $item->pk_id ?></th>
			<th>
				<div class="col_scrollable">
					<a href="<?php echo $base_url.'each_index_revision/'.$item->pk_id ?>">
						<?php echo $item->model_obj[$subject]; ?>&nbsp;
						履歴閲覧
					</a>
				</div>
			</th>
			<td><?php echo $item->operation; ?></td>
			<td><?php echo \Model_Usr::get_display_name($item->user_id); ?></td>
			<td><?php echo $item->created_at; ?></td>
			<td><div class="col_scrollable"><?php echo $item->comment; ?></div></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>編集履歴が存在しません</p>
<?php endif; ?>

