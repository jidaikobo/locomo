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
					if (\Auth::has_access('\Controller_XXX/purge')):
						echo Html::anchor('xxx/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
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
</table><!--/.datatable-->
<?php else: ?>
<p>項目が存在しません。</p>
<?php endif; ?>
