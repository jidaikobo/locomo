<?php
$controller = strtolower(str_replace('Controller_', '', substr(\Request::main()->controller, -strrpos(\Request::main()->controller, '\\'))));
		$summary_types = array(
			'daily' => '日計',
			'monthly' => '月計',
			'yearly' => '年度計',
		);
		// array_merge で空欄を追加する
		$model = $controller == 'support' ? '\Support\Model_Support' : '\Contribute\Model_Contribute';
		$supprt_aim_options = array_merge(array('' => ''), $model::$_type_config['support_aim']);
		$consignee_type_optins = array_merge(array('' => ''), $model::$_type_config['consignee_type']);
		$subject_model = $controller == 'support' ? '\Support\Model_Subject' : '\Contribute\Model_Subject';
		$subject_options = array_merge(array('' => ''), $subject_model::get_options(array(), 'name'));

		$is_deleted =  false; ///

?>


<h2><?php echo $title; ?>一覧 (<?php echo \Pagination::get('total_items') ?>)</h2>

<?php
// 検索用 form
if( ! \Input::get('create') && ! @$is_revision):
	echo \Form::open(array('method' => 'get'));

	echo \Form::label('集計', 'summary');
		echo \Form::select('summary', \Input::get('summary'), array('' => '指定しない', 'monthly' => '月別', 'daily' => '日別'));

	echo \Form::label('受付日', 'date');
		echo \Form::input('date', \Input::get('date'), array('class' => 'datetime', 'placeholder' => date('Y-m-d') . '未入力で現在の日付を使用します'));


	echo \Form::label('顧客ID', 'id');
		echo \Form::input('id', \Input::get('id'));
	echo \Form::label('寄付者名', 'name');
		echo \Form::input('name', \Input::get('name'));

	echo \Form::label('寄付者カナ', 'kana');
		echo \Form::input('kana', \Input::get('kana'));
	echo \Form::label('寄付者住所', 'address');
		echo \Form::input('address', \Input::get('address'));
	echo \Form::label('電話番号', 'tel');
		echo \Form::input('tel', \Input::get('tel'));

	echo \Form::submit('submit', '検索', array('class'=>'button'));
	echo \Form::close();
endif;
?>




<?php if ($items): ?>
<?php
// 印刷用 form
// todo uri
echo \Form::open(array('action' => Uri::create($controller . '/letter', array(), \Input::get())));

echo \Form::submit('checked_all', '全選択', array('class'=>'button'));
echo \Form::submit('checked_all_clear', '全選択解除', array('class'=>'button'));
echo \Form::close();
echo \Form::open(array('action' => Uri::create('pdf/letter', array())));
echo \Form::label('礼状の日付', 'letter_date');
echo \Form::input('letter_date', \Input::get('date'), array('class' => 'datetime', 'placeholder' => date('Y-m-d')));
?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>No</th>
			<th>礼状印刷</th>
			<th>顧客ID</th>
			<th>寄付者</th>
			<th>受付日</th>
			<th>寄付ID</th>
			<th>寄付金額</th>
			<th>寄付物品名</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php
$no = 1;
foreach ($items as $item):
?>		<tr>
			<td><?php echo $no; // todo いる? ?></td>
			<td><?php echo Form::checkbox('ids[]', $item->id, $checked_all);?></td>
			<td><?php echo $item->customer['id']; ?></td>
			<td><?php echo $item->customer['name']; ?></td>
			<td><?php echo date('Y/n/d', strtotime($item->receipt_at)); ?></td>
			<td><?php echo $item->id; ?></td>
			<td><?php echo $item->support_money; ?></td>
			<td><?php echo $item->support_article; ?></td>
			<td>
				<div class="btn-toolbar">
					<div class="btn-group">
						<?php
						$delete_ctrl = $is_deleted ? 'confirm_delete' : 'delete' ;
						echo Html::anchor($controller . '/view'.'/'.$item->id, 'View', array('class' => 'button'));
						echo Html::anchor($controller . '/edit'.'/'.$item->id, '<i class="icon-wrench"></i> Edit', array('class' => 'button'));
						?>
					</div>
				</div>

			</td>
		</tr>
<?php
$no++;
endforeach;
?>
	</tbody>
</table>
<?php
	echo \Form::csrf();
	echo \Form::submit('submit', '礼状印刷', array('class'=>'button'));
	echo \Form::close();
?>

<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>存在しません。</p>

<?php endif; ?>





