<?php echo $search_form; ?>

<?php if ($items): ?>

<!--ページネーション-->
<div class="index_toolbar clearfix">
<?php echo \Pagination::create_links(); ?>
</div>

<!--一覧-->
<table class="tbl datatable tbl_scrollable lcm_focus" title="ユーザ一覧">
	<thead>
		<tr>
			<!--
			<th style="width: 10px; padding-right: 3px; padding-left: 3px;"><a role="button" class="button" style="padding: 4px 4px 2px; margin: 0;">選択</a></th>
			-->
			<th class="min"><?php echo \Pagination::sort('id', 'ID');?></th>
			<th style="width:8em;"><?php echo \Pagination::sort('username', 'ユーザ名');?></th>
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
<!--			<td style="text-align: center;"><input type="checkbox"></td>-->
			<td class="ar"><?php echo $item->id; ?></td>
			<td><?php echo $item->title_text; ?></td>
			<td><?php echo $item->deleted_at; ?></td>
			<td class="min">
				<div class="btn_group">
					<?php
					$ctrl = $locomo['controller']['name'] == '\Controller_Scdl_Admin' ? 'scdl' :'reserve/reserve' ;

					if (\Auth::is_admin()):
						echo Html::anchor($ctrl.'/viewdetail/'.$item->id, '<span class="skip">'.'を</span>閲覧', array('class' => 'view'));
						echo Html::anchor($ctrl.'/edit/'.$item->id, '編集', array('class' => 'edit'));
						if ($item->deleted_at):
							echo Html::anchor($ctrl.'/undelete/'.$item->id, '復活', array('class' => 'undelete confirm'));
							echo Html::anchor($ctrl.'/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
						else:
							echo Html::anchor($ctrl.'/delete/'.$item->id, '削除', array('class' => 'delete confirm'));
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
			<?php if (\Request::main()->action == 'index_deleted'): ?>
				<th>削除された日</th>
			<?php endif; ?>
			<th>操作</th>
		</tr>
	</tfoot>
</table>
<?php endif; ?>
