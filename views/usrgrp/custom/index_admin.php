<?php //echo $search_form; ?>

<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus" title="項目一覧">
	<thead>
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
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
	<tr>
		<td class="ar"><?php echo $item->id; ?></td>
		<td><a href="<?php echo \Uri::create('/usrgrp/custom/view/'.$item->id) ?>"><?php echo $item->name; ?></a></td>
		<?php if (\Auth::is_admin()): ?>
		<td class="ac"><div class="col_scrollable" tabindex="-1"><?php echo $item->is_for_acl ? 'yes' : 'no' ; ?></div></td>
		<?php endif; ?>
		<?php if (\Request::main()->action == 'index_deleted'): ?>
			<td><?php echo $item->deleted_at; ?></td>
		<?php endif; ?>
		<td class="min">
			<div class="btn_group">
				<?php
				if (\Auth::has_access('\Controller_Usrgrp_Custom/view')):
					echo Html::anchor('usrgrp/custom/view/'.$item->id, '<span class="skip">'.$item->name.'を</span>閲覧', array('class' => 'view'));
				endif;
				if (\Auth::has_access('\Controller_Usrgrp_Custom/edit')):
					echo Html::anchor('usrgrp/custom/edit/'.$item->id, '編集', array('class' => 'edit'));
				endif;
				if (\Auth::has_access('\Controller_Usrgrp_Custom/delete')):
					echo Html::anchor('usrgrp/custom/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
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

