<?php echo \View::forge('inc_header'); ?>

<h2>Listing <span class='muted'>Usergroups</span> (<?php echo $hit ?>)</h2>
<br>
<?php if ($items): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Usergroup name</th>
			<th>Delete date</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>

			<td><?php echo \Str::truncate($item->usergroup_name,20); ?></td>
			<td><?php echo $item->deleted_at; ?></td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						<?php
						$ctrl_sfx = $is_deleted ? '_deleted' : '' ;
						$delete_ctrl = $is_deleted ? 'confirm_delete' : 'delete' ;
						echo Html::anchor('usergroup/view'.$ctrl_sfx.'/'.$item->id, '<i class="icon-eye-open"></i> View', array('class' => 'btn btn-small'));
						echo Html::anchor('usergroup/edit'.$ctrl_sfx.'/'.$item->id, '<i class="icon-wrench"></i> Edit', array('class' => 'btn btn-small'));
						if($is_deleted):
							echo Html::anchor('usergroup/undelete/'.$item->id, '<i class="icon-trash icon-white"></i> Undelete', array('class' => 'btn btn-small'));
							echo Html::anchor('usergroup/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'btn btn-small btn-danger'));
						else:
							echo Html::anchor('usergroup/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'btn btn-small btn-danger', 'onclick' => "return confirm('Are you sure?')", 'onkeypress' => "return confirm('Are you sure?')"));
						endif;
						?>
					</div>
				</div>

			</td>
		</tr>
<?php endforeach; ?>	</tbody>
</table>
<?php echo $pagination; ?>

<?php else: ?>
<p>No Usergroups.</p>

<?php endif; ?><p>
	<?php echo Html::anchor('usergroup/create', 'Add new Usergroup', array('class' => 'btn btn-success')); ?>

</p>

<?php echo \View::forge('inc_footer');
