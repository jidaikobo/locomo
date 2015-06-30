<?php echo $search_form; ?>

<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus" title="項目一覧">
	<thead>
		<tr>
			<th class="min"><?php echo \Pagination::sort('id', 'ID');?></th>
			<th><?php echo \Pagination::sort('name', 'グループ名');?></th>
			<?php if (\Auth::is_admin()): ?>
			<th class="min"><?php echo \Pagination::sort('is_for_acl', '権限用'); ?></th>
			<?php endif; ?>
			<th class="min">操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
	<tr title="<?php echo $item->name; ?>" tabindex="-1">
		<td class="ar"><?php echo $item->id; ?></td>
		<td><div class="col_scrollable" style="min-width: 10em;"><a href="<?php echo \Uri::create('/usrgrp/view/'.$item->id) ?>"><?php echo $item->name; ?></a></div></td>
		<?php if (\Auth::is_admin()): ?>
		<td class="ac"><?php echo $item->is_for_acl ? '権限用' : '表示用' ; ?></td>
		<?php endif; ?>
		<td class="min">
			<div class="btn_group">
				<?php
				if (\Auth::has_access('\Controller_Usrgrp/view')):
					echo Html::anchor('usrgrp/view/'.$item->id, '<span class="skip">'.$item->name.'を</span>閲覧', array('class' => 'view'));
				endif;
				if (\Auth::has_access('\Controller_Usrgrp/edit')):
					echo Html::anchor('usrgrp/edit/'.$item->id, '編集', array('class' => 'edit'));
				endif;
				if (\Auth::has_access('\Controller_Usrgrp/delete')):
					echo Html::anchor('usrgrp/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
				endif;
				?>
			</div>
		</td>
		</tr><?php endforeach; ?>
	</tbody>
	<tfoot class="thead">
		<tr>
			<th class="min"><?php echo \Pagination::sort('id', 'ID');?></th>
			<th><?php echo \Pagination::sort('name', 'グループ名');?></th>
<?php if (\Auth::is_admin()): ?>
			<th class="min"><?php echo \Pagination::sort('is_for_acl', '権限用'); ?></th>
<?php endif; ?>
			<?php if (\Request::main()->action == 'index_deleted'): ?>
				<th class="min">削除された日</th>
			<?php endif; ?>
			<th class="min">操作</th>
		</tr>
	</tfoot>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>項目が存在しません</p>

<?php endif; ?>

