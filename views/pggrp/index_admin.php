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
			<th><?php echo \Pagination::sort('name', 'グループ名', false);?></th>

			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><div class="col_scrollable"><?php echo $item->name; ?></div></td>
			<td>
				<div class="btn_group">
					<?php
					if (\Auth::has_access('\Controller_Pggrp/view')):
						echo Html::anchor('pggrp/view/'.$item->id, '閲覧', array('class' => 'view'));
					endif;
					if (\Auth::has_access('\Controller_Pggrp/edit')):
						echo Html::anchor('pggrp/edit/'.$item->id, '編集', array('class' => 'edit'));
					endif;
					if (\Auth::has_access('\Controller_Pggrp/delete')):
						if ($item->deleted_at):
							echo Html::anchor('pggrp/undelete/'.$item->id, '復活', array('class' => 'undelete confirm'));
							echo Html::anchor('pggrp/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
						else:
							echo Html::anchor('pggrp/delete/'.$item->id, '削除', array('class' => 'delete confirm'));
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
			<th><?php echo \Pagination::sort('name', 'グループ名', false);?></th>

			<th>操作</th>
		</tr>
	</tfoot>
</table><!--/.datatable-->
<?php else: ?>
<p>項目が存在しません。</p>
<?php endif; ?>
