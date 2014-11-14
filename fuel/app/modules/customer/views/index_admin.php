
<h2>項目一覧 (<?php echo \Pagination::get('total_items') ?> <?php echo \Pagination::sort_info('\Customer\Model_Customer') ;?>)</h2>
<?php if ($items): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo Pagination::sort('id', 'ID') ?></th>
			<th><?php echo Pagination::sort('name', '名前') ?></th>
			<th><?php echo Pagination::sort('kana', 'フリガナ') ?></th>
			<th><?php echo Pagination::sort('zip', '郵便番号') ?></th>
			<th><?php echo Pagination::sort('address', '住所') ?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>

			<td><?php echo $item->id; ?></td>
			<td><?php echo $item->name; ?></td>
			<td><?php echo $item->kana; ?></td>
			<td><?php echo $item->zip; ?></td>
			<td><?php echo $item->address; ?></td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
<?php
						//$delete_ctrl = $is_deleted ? 'confirm_delete' : 'delete' ;
						echo Html::anchor('customer/view'.'/'.$item->id, 'View', array('class' => 'button'));
						echo Html::anchor('customer/edit'.'/'.$item->id, '<i class="icon-wrench"></i> Edit', array('class' => 'button'));
						/*
						if($is_deleted):
							echo Html::anchor('customer/undelete/'.$item->id, '<i class="icon-trash icon-white"></i> Undelete', array('class' => 'button'));
							echo Html::anchor('customer/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'button btn-danger'));
						else:
							echo Html::anchor('customer/'.$delete_ctrl.'/'.$item->id, '<i class="icon-trash icon-white"></i> Delete', array('class' => 'button btn-danger', 'onclick' => "return confirm('Are you sure?')", 'onkeypress' => "return confirm('Are you sure?')"));
endif;
*/
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
<p>customerが存在しません。</p>

<?php endif; ?>

