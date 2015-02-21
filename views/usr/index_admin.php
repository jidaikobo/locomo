<?php echo $search_form; ?>

<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus">
	<thead>
		<tr>
			<!--
			<th style="width: 10px; padding-right: 3px; padding-left: 3px;"><a role="button" class="button" style="padding: 4px 4px 2px; margin: 0;">選択</a></th>
			-->
			<th class="min"><?php echo \Pagination::sort('id', 'ID');?></th>
			<th style="width:8em;"><?php echo \Pagination::sort('username', 'ユーザ名');?></th>
			<th style="width:8em;"><?php echo \Pagination::sort('display_name', '表示名'); ?></th>
<?php if (\Auth::is_admin()): ?>
			<th><?php echo \Pagination::sort('email', 'Email'); ?></th>
<?php endif; ?>
			<th><?php echo \Pagination::sort('last_login_at', '最後のログイン日時'); ?></th>
			<?php if (\Request::main()->action == 'index_deleted'): ?>
				<th>削除された日</th>
			<?php endif; ?>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1" title="<?php echo $item->display_name ?>">
<!--			<td style="text-align: center;"><input type="checkbox"></td>-->
			<td class="ar"><?php echo $item->id; ?></td>
			<td style="min-width: 8em;"><div class="col_scrollable" tabindex="-1"><?php echo $item->username; ?></div></th>
			<th style="min-width: 8em;"><div class="col_scrollable" tabindex="-1"><?php echo $item->display_name; ?></div>
			</td>
<?php if (\Auth::is_admin()): ?>
			<td style="min-width: 12em;"><div class="col_scrollable" tabindex="-1"><?php echo $item->email; ?></div></td>
<?php endif; ?>
			<td><?php echo $item->last_login_at != '0000-00-00 00:00:00' ? $item->last_login_at : ''; ?></td>
			<?php if (\Request::main()->action == 'index_deleted'): ?>
				<td><?php echo $item->deleted_at; ?></td>
			<?php endif; ?>
			<td class="min">
				<div class="btn_group">
					<?php
					if (\Auth::has_access('\Controller_Usr::action_view')):
						echo Html::anchor('usr/view/'.$item->id, '<span class="skip">'.$item->display_name.'を</span>閲覧', array('class' => 'view'));
					endif;
					if (\Auth::has_access('\Controller_Usr::action_edit')):
						echo Html::anchor('usr/edit/'.$item->id, '編集', array('class' => 'edit'));
					endif;
					if (\Auth::has_access('\Controller_Usr::action_delete')):
						if ($item->deleted_at):
							echo Html::anchor('usr/undelete/'.$item->id, '復活', array('class' => 'undelete confirm'));
							echo Html::anchor('usr/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
						else:
							echo Html::anchor('usr/delete/'.$item->id, '削除', array('class' => 'delete confirm'));
						endif;
					endif;
					?>
				</div>
			</td>
		</tr><?php endforeach; ?>
	</tbody>
	<tfoot class="thead">
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th><?php echo \Pagination::sort('username', 'ユーザ名');?></th>
			<th><?php echo \Pagination::sort('display_name', '表示名'); ?></th>
			<th><?php echo \Pagination::sort('email', 'Email'); ?></th>
			<th><?php echo \Pagination::sort('last_login_at', '最後のログイン日時'); ?></th>
			<?php if (\Request::main()->action == 'index_deleted'): ?>
				<th>削除された日</th>
			<?php endif; ?>
			<th>操作</th>
		</tr>
	</tfoot>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>項目が存在しません</p>

<?php endif; ?>

