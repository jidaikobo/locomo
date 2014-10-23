<?php echo render('inc_header'); ?>

<h2>項目一覧 (<?php echo $hit ?>)</h2>
<p><?php echo \Pagination::sort_info('\User\Model_User'); ?></p>
<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th><?php echo \Pagination::sort('user_name', 'User name'); ?></th>
			<th><?php echo \Pagination::sort('email', 'Email'); ?></th>
			<th><?php echo \Pagination::sort('last_login_at', 'Last login'); ?></th>
			<th>Delete date</th>
			<th>Status</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<td><?php echo $item->id; ?></td>
			<td><span class="col_scroll" style="min-width: 5em;" tabindex="-1"><?php 
					echo Html::anchor('user/view'.'/'.$item->id, $item->display_name, array('class' => 'view'));?></span></td>
			<td><div class="col_scroll" style="min-width: 10em;"  tabindex="-1"><?php echo $item->email; ?></div></td>
			<td><?php echo $item->last_login_at; ?></td>
			<td><?php echo $item->deleted_at; ?></td>
			<td><?php echo $item->status; ?></td>
			<td>
				<div class="btn_group">
					<?php
					echo Html::anchor('user/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					?>
				</div>
			</td>
		</tr><?php endforeach; ?>
	</tbody>
</table>
<?php echo $pagination; ?>

<?php else: ?>
<p>ユーザが存在しません。</p>

<?php endif; ?>

<p><?php echo Html::anchor('user/create', 'Add new User', array('class' => 'btn btn-success')); ?></p>

<?php echo render('inc_footer'); ?>
