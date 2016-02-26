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

			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
			<td>
				<div class="btn_group">
					<?php
					if (\Auth::has_access('\Controller_Srch/view')):
						echo Html::anchor('srch/view/'.$item->id, '閲覧', array('class' => 'view'));
					endif;
					if (\Auth::has_access('\Controller_Srch/edit')):
						echo Html::anchor('srch/edit/'.$item->id, '編集', array('class' => 'edit'));
					endif;
					if (\Auth::has_access('\Controller_Srch/delete')):
						if ($item->deleted_at):
							echo Html::anchor('srch/undelete/'.$item->id, '復活', array('class' => 'undelete confirm'));
							echo Html::anchor('srch/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
						else:
							echo Html::anchor('srch/delete/'.$item->id, '削除', array('class' => 'delete confirm'));
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

			<th>操作</th>
		</tr>
	</tfoot>
</table><!--/.datatable-->
<?php else: ?>
<p>項目が存在しません。</p>
<?php endif; ?>
