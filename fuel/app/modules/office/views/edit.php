
<h2>Editing <span class='muted'>Support</span></h2>



<!-- template history -->
<label>寄付者</label>
<p><?php echo $customer->id; ?></p>
<p><?php echo $customer->name; ?></p>

<table class="table table-striped">
	<thead>
		<tr>
			<th>受付日</th>
			<th>寄付ID</th>
			<th>寄付金額</th>
			<th>科目</th>
			<th>寄付物品名</th>
			<th>目的</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($histories as $history): ?>		<tr>
			<td><?php echo $history->receipt_at; ?></td>
			<td><?php echo $history->id; ?></td>
			<td><?php echo $history->support_money; ?></td>
			<td><?php echo $history->subject['name']; ?></td>
			<td><?php echo $history->support_article; ?></td>
			<td><?php echo $history->support_aim; ?></td>
<?php endforeach; ?>
	</tbody>
</table>




<?php echo render('_form'); ?>

<p>
	<?php
	if(@$is_revision):
		echo Html::anchor('support/index_revision/'.$item->controller_id, '履歴一覧に戻る',array('class'=>'button'));
		echo Html::anchor('support/edit/'.$item->controller_id, '編集画面に戻る',array('class'=>'button'));
	else:
		//コントローラがリビジョンをサポートしていない場合、この箇所だけで十分です。
		echo Html::anchor('support/view/'.$item->id, '表示',array('class'=>'button'));
		echo Html::anchor('support', '一覧に戻る',array('class'=>'button'));
	endif;
	?>
</p>

