<?php echo $search_form; ?>

<?php if ($items): ?>

<!--.index_toolbar-->
<div class="index_toolbar clearfix">
<!--.index_toolbar_buttons-->
<div class="index_toolbar_buttons">
</div><!-- /.index_toolbar_buttons -->
<?php echo \Pagination::create_links(); ?>
</div><!-- /.index_toolbar -->

<!--.datatable-->
<table class="tbl datatable">
	<thead>
		<tr>
			<th class="ar min"><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th><?php echo \Pagination::sort('title', '表題', false);?></th>
<?php if (\Request::main()->action == 'index_deleted'): ?>
			<th><?php echo \Pagination::sort('deleted_at', '削除日', false);?></th>
<?php else: ?>
			<th><?php echo \Pagination::sort('created_at', '作成日', false);?></th>
<?php endif; ?>
<?php if (\Request::main()->action == 'index_expired'): ?>
			<th><?php echo \Pagination::sort('expired_at', '期限切れ日', false);?></th>
<?php endif; ?>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>

<?php foreach ($items as $item): ?>		<tr>
		<td><?php echo $item->id; ?></td>
		<td><div class="col_scrollable"><?php echo $item->title; ?></div></td>
<?php if (\Request::main()->action == 'index_deleted'): ?>
		<td><?php echo $item->deleted_at; ?></td>
<?php else: ?>
		<td><?php echo $item->created_at; ?></td>
<?php endif; ?>
<?php if (\Request::main()->action == 'index_expired'): ?>
		<td><?php echo $item->expired_at; ?></td>
<?php endif; ?>
		<td>
			<div class="btn_group">
				<?php
				if (\Auth::has_access('\Controller_Pg/view')):
					echo Html::anchor('pg/'.$item->path, '閲覧', array('class' => 'view'));
				endif;
				if (\Auth::has_access('\Controller_Pg/edit')):
					echo Html::anchor('pg/edit/'.$item->id, '編集', array('class' => 'edit'));
				endif;
				if (\Auth::has_access('\Controller_Pg/delete')):
					if ($item->deleted_at):
						echo Html::anchor('pg/undelete/'.$item->id, '復活', array('class' => 'undelete confirm'));
						echo Html::anchor('pg/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
					else:
						echo Html::anchor('pg/delete/'.$item->id, '削除', array('class' => 'delete confirm'));
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
			<th class="ar min"><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th><?php echo \Pagination::sort('title', '表題', false);?></th>
<?php if (\Request::main()->action == 'index_deleted'): ?>
			<th><?php echo \Pagination::sort('deleted_at', '削除日', false);?></th>
<?php else: ?>
			<th><?php echo \Pagination::sort('created_at', '作成日', false);?></th>
<?php endif; ?>
<?php if (\Request::main()->action == 'index_expired'): ?>
			<th><?php echo \Pagination::sort('expired_at', '期限切れ日', false);?></th>
<?php endif; ?>
			<th>操作</th>
		</tr>
	</tfoot>
</table><!--/.datatable-->
<?php else: ?>
<p>項目が存在しません。</p>
<?php endif; ?>
