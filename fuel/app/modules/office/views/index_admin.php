<?php if ($items): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>id</th>
			<th>受取日</th>
			<th>顧客</th>
			<th>金額</th>
			<th>科目</th>
			<th>目的</th>
			<th>受取方法</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>

			<td><?php echo $item->id; ?></td>
			<td><?php echo date('Y年 m月d日', strtotime($item->receipt_at)); ?></td>
			<td><?php echo $item->customer['name']; ?></td>
			<td><?php echo $item->support_money; ?></td>
			<td><?php echo $item->subject['name']; ?></td>
			<td><?php echo $item->support_aim; ?></td>
			<td><?php echo $item->consignee_type; ?></td>
			<td></td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						<?php
						// $delete_ctrl = $is_deleted ? 'confirm_delete' : 'delete' ;
						echo Html::anchor('/office/' . $controller . '/view'.'/'.$item->id, 'View', array('class' => 'button'));
						echo Html::anchor('/office/' . $controller . '/edit'.'/'.$item->id, '<i class="icon-wrench"></i> Edit', array('class' => 'button'));
						if(! is_null($item->deleted_at)):
							echo Html::anchor('/office/' . $controller . '/undelete/'.$item->id, '<i class="icon-trash icon-white"></i> Undelete', array('class' => 'button'));
							echo Html::anchor('/office/' . $controller . '/purge/'.$item->id, '<i class="icon-trash icon-white"></i> Purge', array('class' => 'button btn-danger'));
						else:
							echo Html::anchor('/office/' . $controller . '/delete/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'button btn-danger', 'onclick' => "return confirm('Are you sure?')", 'onkeypress' => "return confirm('Are you sure?')"));
						endif;
						?>
					</div>
				</div>

			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>存在しません。</p>

<?php endif; ?>




