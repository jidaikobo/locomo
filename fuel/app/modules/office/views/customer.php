<h2><?php echo $title; ?> (<?php echo \Pagination::get('total_items') ?>)</h2>


<?php if ($items): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>選択</th>
			<th>顧客ID</th>
			<th>顧客名</th>
			<th>住所</th>
			<th>電話番号</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
			<td>
				<div class="btn-toolbar">
					<?php echo Html::anchor('support/create/?customer_id=' . $item->id, '選択', array('class' => 'button')); ?>
				</div>
			</td>
			<td><?php echo $item->id; ?></td>
			<td><?php echo $item->name . $item->kana; ?></td>
			<td><?php echo $item->address; ?></td>
			<td><?php echo $item->tel; ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>存在しません。</p>

<?php endif; ?>
