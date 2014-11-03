
<h2>ワークフロー一覧 (<?php echo $hit ?>)</h2>
<br>
<?php if ($items): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>id</th>
			<th>ワークフロー名</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr>
			<td><?php echo $item->id; ?></td>
			<td><?php echo $item->name; ?></td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						<?php
						$delete_ctrl = $is_deleted ? 'confirm_delete' : 'delete' ;
						echo Html::anchor('workflowadmin/setup'.'/'.$item->id, '<i class="icon-wrench"></i> 設定', array('class' => 'button'));
						echo Html::anchor('workflowadmin/view'.'/'.$item->id, '閲覧', array('class' => 'button'));
						echo Html::anchor('workflowadmin/edit'.'/'.$item->id, '<i class="icon-wrench"></i> 編集', array('class' => 'button'));
						if($is_deleted):
							echo Html::anchor('workflowadmin/undelete/'.$item->id, '<i class="icon-trash icon-white"></i> 復活', array('class' => 'button'));
							echo Html::anchor('workflowadmin/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> 削除', array('class' => 'button btn-danger'));
						else:
							echo Html::anchor('workflowadmin/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> 削除', array('class' => 'button btn-danger', 'onclick' => "return confirm('削除してよろしいですか？')", 'onkeypress' => "return confirm('削除してよろしいですか？')"));
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
<p>項目が存在しません。</p>

<?php endif; ?>


