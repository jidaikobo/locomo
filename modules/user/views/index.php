<h2>項目一覧<?php echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : ''; ?></h2>
<p><?php echo \Pagination::sort_info('\User\Model_User'); ?></p>
<br>
<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false); ?></th>
			<th><?php echo \Pagination::sort('username', 'User name'); ?></th>
			<th><?php echo \Pagination::sort('email', 'Email'); ?></th>
			<th><?php echo \Pagination::sort('last_login_at', 'Last login'); ?></th>
			<th>Delete date</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<td><?php echo $item->id; ?></td>
			<td style="min-width: 6em;" ><div class="col_scrollable"tabindex="-1"><?php 
					echo Html::anchor('user/view'.'/'.$item->id, $item->display_name, array('class' => 'view'));?></div></td>
			<td style="min-width: 12em;"><div class="col_scrollable" tabindex="-1"><?php echo $item->email; ?></div></td>
			<td><?php echo $item->last_login_at; ?></td>
			<td><?php echo $item->deleted_at; ?></td>
			<td>
				<div class="btn_group">
					<?php
					echo Html::anchor('user/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					?>
				</div>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>ユーザが存在しません。</p>

<?php endif; ?>

<style>
th a.asc:after{
	content: '[↓]';
}
th a.desc:after{
	content: '[↑]';
}
</style>
