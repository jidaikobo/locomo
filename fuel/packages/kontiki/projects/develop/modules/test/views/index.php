<?php echo $include_tpl('inc_header.php'); ?>

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
						echo Html::anchor('test/view'.'/'.$item->id, 'View', array('class' => 'button'));
						echo Html::anchor('test/edit'.'/'.$item->id, '<i class="icon-wrench"></i> Edit', array('class' => 'button'));
						if($is_deleted):
							echo Html::anchor('test/undelete/'.$item->id, '<i class="icon-trash icon-white"></i> Undelete', array('class' => 'button'));
							echo Html::anchor('test/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'button btn-danger'));
						else:
							echo Html::anchor('test/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'button btn-danger', 'onclick' => "return confirm('Are you sure?')", 'onkeypress' => "return confirm('Are you sure?')"));
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
<p>testが存在しません。</p>

<?php endif; ?>

<?php echo $include_tpl('inc_footer.php'); ?>
