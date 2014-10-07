<?php echo render('inc_header'); ?>

<h2>項目一覧 (<?php echo $hit ?>)</h2>
<br>
<?php if ($items): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>id</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>

			<td><?php echo $item->id; ?></td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						<?php
						$delete_ctrl = $is_deleted ? 'confirm_delete' : 'delete' ;
						echo Html::anchor('post/view'.'/'.$item->id, '<i class="icon-eye-open"></i> View', array('class' => 'btn btn-small'));
						echo Html::anchor('post/edit'.'/'.$item->id, '<i class="icon-wrench"></i> Edit', array('class' => 'btn btn-small'));
						if($is_deleted):
							echo Html::anchor('post/undelete/'.$item->id, '<i class="icon-trash icon-white"></i> Undelete', array('class' => 'btn btn-small'));
							echo Html::anchor('post/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'btn btn-small btn-danger'));
						else:
							echo Html::anchor('post/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'btn btn-small btn-danger', 'onclick' => "return confirm('Are you sure?')", 'onkeypress' => "return confirm('Are you sure?')"));
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
<p>postが存在しません。</p>

<?php endif; ?>

<?php echo render('inc_footer'); ?>
