<?php echo $include_tpl('inc_header.php'); ?>

<h2>項目一覧 (<?php echo $hit ?>)</h2>
<br>
<?php if ($items): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>id</th>
			<th>User name</th>
			<th>Email</th>
			<th>Last login</th>
			<th>Delete date</th>
			<th>Status</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>

			<td><?php echo $item->id; ?></td>
			<td><?php echo \Str::truncate($item->user_name, 20); ?></td>
			<td><?php echo \Str::truncate($item->email, 20); ?></td>
			<td><?php echo $item->last_login_at; ?></td>
			<td><?php echo $item->deleted_at; ?></td>
			<td><?php echo $item->status; ?></td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						<?php
						$delete_ctrl = $is_deleted ? 'confirm_delete' : 'delete' ;
						echo Html::anchor('user/view'.'/'.$item->id, 'View', array('class' => 'button'));
						echo Html::anchor('user/edit'.'/'.$item->id, '<i class="icon-wrench"></i> Edit', array('class' => 'button'));
						if($is_deleted):
							echo Html::anchor('user/undelete/'.$item->id, '<i class="icon-trash icon-white"></i> Undelete', array('class' => 'button'));
							echo Html::anchor('user/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'button btn-danger'));
						else:
							echo Html::anchor('user/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'button btn-danger', 'onclick' => "return confirm('Are you sure?')", 'onkeypress' => "return confirm('Are you sure?')"));
						endif;
						?>
					</div>
				</div>

			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo $pagination; ?>

<?php else: ?>
<p>ユーザが存在しません。</p>

<?php endif; ?><p>
	<?php echo Html::anchor('user/create', 'Add new User', array('class' => 'btn btn-success')); ?>

</p>

<?php echo $include_tpl('inc_footer.php'); ?>
