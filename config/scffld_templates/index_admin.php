<?php echo $search_form; ?>

<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
###THEAD###
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
###TBODY###			<td>
				<div class="btn_group">
					<?php
					if (\Auth::has_access('\Controller_XXX/view')):
						echo Html::anchor('xxx/view/'.$item->id, '閲覧', array('class' => 'view'));
					endif;
					if (\Auth::has_access('\Controller_XXX/edit')):
						echo Html::anchor('xxx/edit/'.$item->id, '編集', array('class' => 'edit'));
					endif;
					if (\Auth::has_access('\Controller_XXX/delete')):
						if ($item->deleted_at):
							echo Html::anchor('xxx/undelete/'.$item->id, '復活', array('class' => 'undelete confirm'));
							echo Html::anchor('xxx/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
						else:
							echo Html::anchor('xxx/delete/'.$item->id, '削除', array('class' => 'delete confirm'));
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
###THEAD###
			<th>操作</th>
		</tr>
	</tfoot>
</table>
<?php echo \Pagination::create_links(); ?>
<?php else: ?>
<p>xxxが存在しません。</p>
<?php endif; ?>
