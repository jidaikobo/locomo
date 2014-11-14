<?php
if (! \Request::is_hmvc()) {
echo \Form::open(array('method' => 'get'));
echo \Form::label('集計年度');
echo \Form::input('year');
echo \Form::submit('submit', '一覧表示');
echo \Form::close();
}
?>

<style>
table {
width: 100%;
margin-bottom: 150px;
}
th {
border: 1px solid #ccc;
background-color: #393;
color: white;
}
thead th {
background-color: #339;
}
td {
text-align: right;
border-top: 1px solid #ccc;
border-right: 1px solid #ccc;
}
.description {
float:right;
margin-left: 180mm;
}

</style>

	<h2><?php echo $title; ?></h2>
	<ul class="description">
		<li><?php date('Y年 n月d日') ?> 現在</li>
		<li>上段: 件数</li>
		<li>下段: 金額</li>
		<li><?php echo $total['sum_total'] ?></li>
	</ul>

<table>
	<thead>
		<tr>
			<th>年月</th>
		<?php foreach ($table_keys as $key) : ?>
		 	<th><?php echo $key; ?></th>
		<?php endforeach; ?>
			<th>合計</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($monthly as $month => $value) : ?>
		<tr>
			<th>
				<?php echo $month > 3 ? $year : $year + 1 ;?> 年
				<?php echo $month;?> 月
			</th>
		<?php foreach ($table_keys as $key => $val) : ?>
			<td>
				<?php echo isset($value['sum'][$key]) ? $value['sum'][$key] : 0; ?>
			</td>
		<?php endforeach; ?>
			<td><?php echo $value['sum_total'] ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>

	<tfoot>
		<tr>
			<th>
				合計
			</th>
		<?php foreach ($table_keys as $key => $val) : ?>
			<td>
				<?php echo isset($total['sum'][$key]) ? $total['sum'][$key] : 0; ?>
			</td>
		<?php endforeach; ?>
			<td><?php echo $total['sum_total'] ?></td>
		</tr>
	</tfoot>
</table>



<?php
if (! \Request::is_hmvc()) {
// todo
$controller = strtolower(str_replace('Controller_', '', substr(\Request::main()->controller, -strrpos(\Request::main()->controller, '\\'))));

echo \Form::open(array('action' => 'pdf/summary/' . $controller));
echo \Form::hidden('year', $year);
echo \Form::submit('submit', '一覧表印刷');
echo \Form::close();
}
?>

