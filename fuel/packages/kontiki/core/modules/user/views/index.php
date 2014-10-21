<?php echo render('inc_header'); ?>
<h2>項目一覧 (<?php echo $hit ?>)</h2>
<p><?php echo \Sort::sort_info('\User\Model_User'); ?></p>
<br>
<?php if ($items): ?>
<table class="tbl datatable scrollable">
	<thead>
		<tr>
			<th><?php echo \Sort::sort('id', 'ID'); ?></th>
			<th><?php echo \Sort::sort('user_name', 'User name'); ?></th>
			<th><?php echo \Sort::sort('email', 'Email'); ?></th>
			<th><?php echo \Sort::sort('last_login_at', 'Last login'); ?></th>
			<th>Delete date</th>
			<th>Status</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	
<?php foreach ($items as $item): ?>
		<tr>
			<td><?php echo $item->id; ?></td>
			<td><span class="col_scroll" style="min-width: 5em;" tabindex="-1"><?php 
					echo Html::anchor('user/view'.'/'.$item->id, $item->display_name, array('class' => 'view'));?></span></td>
			<td><div class="col_scroll" style="min-width: 10em;"  tabindex="-1"><?php echo $item->email; ?></div></td>
			<td><div class="col_scroll" tabindex="-1"><?php echo $item->last_login_at; ?></div></td>
			<td><?php echo $item->deleted_at; ?>121212121212</td>
			<td><?php echo $item->status; ?></td>
			<td>
				<div class="btn_group">
					<?php
					/*
					$delete_ctrl = $is_deleted ? 'confirm_delete' : 'delete' ;
					echo Html::anchor('user/view'.'/'.$item->id, '閲覧', array('class' => 'view'));
					*/
					echo Html::anchor('user/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					/*
					if($is_deleted):
						echo Html::anchor('user/undelete/'.$item->id, '復活', array('class' => 'undelete'));
						echo Html::anchor('user/'.$delete_ctrl.'/'.$item->id, '削除', array('class' => 'delete'));
					else:
						echo Html::anchor('user/'.$delete_ctrl.'/'.$item->id, '削除', array('class' => 'delete', 'onclick' => "return confirm('Are you sure?')", 'onkeypress' => "return confirm('Are you sure?')"));
					endif;
					*/
					?>
				</div>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo $pagination; ?>

<?php else: ?>
<p>ユーザが存在しません。</p>

<?php endif; ?>

<?php echo render('inc_footer'); ?>


<style>
th a.asc:after{
	content: '[↓]';
}
th a.desc:after{
	content: '[↑]';
}

</style>
