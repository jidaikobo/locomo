<?php echo $search_form; ?>

<?php if ($items): ?>
<?php
// for output
// echo \Form::open(array('method' => 'post', 'action' => \Uri::create('output/xxx/output', array(), \Input::get())));
// echo \Asset::js('toolbarIndexAdmin.js');
?>
<!--.index_toolbar-->
<div class="index_toolbar clearfix">
<!--.index_toolbar_buttons-->
<div class="index_toolbar_buttons">
<?php
// for output
// echo \XXX\Presenter_XXX_Index_Admin::index_admin_toolbar_format('\Format\Model_XXX');
?>
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
					//echo \XXX\Presenter_XXX_Index_Admin::create_ctrls($item);
					if (\Auth::has_access('\XXX\Controller_XXX/view')):
						echo Html::anchor('xxx/view/'.$item->id, '閲覧', array('class' => 'view'));
					endif;
					if (\Auth::has_access('\XXX\Controller_XXX/edit')):
						echo Html::anchor('xxx/edit/'.$item->id, '編集', array('class' => 'edit'));
					endif;
					if (\Auth::has_access('\XXX\Controller_XXX/delete')):
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
</table><!--/.datatable-->
<?php
// for output
// echo \XXX\Presenter_XXX_Index_Admin::index_admin_toolbar_format('\Format\Model_XXX', 1);
// echo \Form::close();
?>
<?php else: ?>
<p>項目が存在しません。</p>
<?php endif; ?>
