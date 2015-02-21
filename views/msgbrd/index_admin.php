<?php echo $search_form; ?>

<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th><?php echo \Pagination::sort('name', '表題', false);?></th>
<!--			<th><?php echo \Pagination::sort('contents', '本文', false);?></th>-->
			<th><?php echo \Pagination::sort('category_id', 'カテゴリ', false);?></th>
			<th><?php echo \Pagination::sort('created_at', '作成日時', false);?></th>
			<th><?php echo \Pagination::sort('updated_at', '更新日時', false);?></th>
			<th><?php echo \Pagination::sort('expired_at', '有効期日', false);?></th>
			<th><?php echo \Pagination::sort('deleted_at', '削除日', false);?></th>
			<th><?php echo \Pagination::sort('is_draft', '公開', false);?></th>
			<th><?php echo \Pagination::sort('creator_id', '投稿者', false);?></th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->name; ?></div></td>
<!--	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->contents; ?></div></td>-->
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->categories['name']; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->created_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->updated_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->expired_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->deleted_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->is_draft ? '下書き' : '公開'; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo \Model_Usr::get_display_name($item->creator_id); ?></div></td>
			<td>
				<div class="btn_group">
					<?php
					if (\Auth::has_access('\Controller_Msgbrd::action_view')):
						echo Html::anchor('msgbrd/view/'.$item->id, '閲覧', array('class' => 'view'));
					endif;
					if (\Auth::has_access('\Controller_Msgbrd::action_edit')):
						echo Html::anchor('msgbrd/edit/'.$item->id, '編集', array('class' => 'edit'));
					endif;
					if (\Auth::has_access('\Controller_Msgbrd::action_delete')):
						if ($item->deleted_at):
							echo Html::anchor('msgbrd/undelete/'.$item->id, '復活', array('class' => 'undelete confirm'));
							echo Html::anchor('msgbrd/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
						else:
							echo Html::anchor('msgbrd/delete/'.$item->id, '削除', array('class' => 'delete confirm'));
						endif;
					endif;
					?>
				</div>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
	<tfoot class="thead">
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th><?php echo \Pagination::sort('name', '表題', false);?></th>
<!--			<th><?php echo \Pagination::sort('contents', '本文', false);?></th>-->
			<th><?php echo \Pagination::sort('category_id', 'カテゴリ', false);?></th>
			<th><?php echo \Pagination::sort('created_at', '作成日時', false);?></th>
			<th><?php echo \Pagination::sort('updated_at', '更新日時', false);?></th>
			<th><?php echo \Pagination::sort('expired_at', '有効期日', false);?></th>
			<th><?php echo \Pagination::sort('deleted_at', '削除日', false);?></th>
			<th><?php echo \Pagination::sort('is_draft', '公開', false);?></th>
			<th><?php echo \Pagination::sort('creator_id', '投稿者', false);?></th>
			<th>操作</th>
		</tr>
	</tfoot>
</table>
<?php echo \Pagination::create_links(); ?>
<?php else: ?>
<p>msgbrdが存在しません。</p>
<?php endif; ?>
