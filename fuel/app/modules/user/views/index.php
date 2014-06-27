<?php echo \View::forge('inc_header'); ?>

<ul>
	<li><a href="/user/index">通常</a></li>
	<li><a href="/user/index_deleted">削除済み</a></li>
	<li><a href="/user/index_yet">予約</a></li>
	<li><a href="/user/index_expired">期限切れ</a></li>
	<li><a href="/user/add_testdata">10件のテストデータ追加</a></li>
</ul>

<h2>Listing <span class='muted'>Users</span> (<?php echo $hit ?>)</h2>
<br>
<?php if ($items): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>id</th>
			<th>User name</th>
			<th>Password</th>
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
			<td><?php echo \Str::truncate($item->password, 20); ?></td>
			<td><?php echo \Str::truncate($item->email, 20); ?></td>
			<td><?php echo $item->last_login_at; ?></td>
			<td><?php echo $item->deleted_at; ?></td>
			<td><?php echo $item->status; ?></td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						<?php
						$ctrl_sfx = $is_deleted ? '_deleted' : '' ;
						$delete_ctrl = $is_deleted ? 'confirm_delete' : 'delete' ;
						echo Html::anchor('user/view'.$ctrl_sfx.'/'.$item->id, '<i class="icon-eye-open"></i> View', array('class' => 'btn btn-small'));
						echo Html::anchor('user/edit'.$ctrl_sfx.'/'.$item->id, '<i class="icon-wrench"></i> Edit', array('class' => 'btn btn-small'));
						if($is_deleted):
							echo Html::anchor('user/undelete/'.$item->id, '<i class="icon-trash icon-white"></i> Undelete', array('class' => 'btn btn-small'));
							echo Html::anchor('user/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'btn btn-small btn-danger'));
						else:
							echo Html::anchor('user/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'btn btn-small btn-danger', 'onclick' => "return confirm('Are you sure?')", 'onkeypress' => "return confirm('Are you sure?')"));
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
<p>No Users.</p>

<?php endif; ?><p>
	<?php echo Html::anchor('user/create', 'Add new User', array('class' => 'btn btn-success')); ?>

</p>

<?php echo \View::forge('inc_footer');
