<?php echo render('inc_header'); ?>

<h2>項目一覧 (<?php echo $hit ?>)</h2>
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
						echo Html::anchor('customer/view'.'/'.$item->id, 'View', array('class' => 'button'));
						echo Html::anchor('customer/edit'.'/'.$item->id, '<i class="icon-wrench"></i> Edit', array('class' => 'button'));
						if($is_deleted):
							echo Html::anchor('customer/undelete/'.$item->id, '<i class="icon-trash icon-white"></i> Undelete', array('class' => 'button'));
							echo Html::anchor('customer/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'button btn-danger'));
						else:
							echo Html::anchor('customer/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'button btn-danger', 'onclick' => "return confirm('Are you sure?')", 'onkeypress' => "return confirm('Are you sure?')"));
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
<p>customerが存在しません。</p>

<?php endif; ?>

<?php echo render('inc_footer'); ?>
