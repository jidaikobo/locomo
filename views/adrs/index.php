<h1>項目一覧<?php echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : ''; ?></h1>

<?php if ($items): ?>
<table class="tbl">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th>氏名</th>
			<th>かな</th>
			<th>会社名</th>
			<th>会社名かな</th>
			<th>電話番号</th>
			<th>FAX番号</th>
			<th>メールアドレス</th>
			<th>携帯電話</th>
			<th>郵便番号</th>
			<th>郵便番号</th>
			<th>住所</th>
			<th>備考</th>
			<th>作成日時</th>
			<th>更新日時</th>
			<th>削除日</th>

		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><?php echo $item->name; ?></td>
	<td><?php echo $item->kana; ?></td>
	<td><?php echo $item->company_name; ?></td>
	<td><?php echo $item->company_kana; ?></td>
	<td><?php echo $item->tel; ?></td>
	<td><?php echo $item->fax; ?></td>
	<td><?php echo $item->mail; ?></td>
	<td><?php echo $item->mobile; ?></td>
	<td><?php echo $item->zip3; ?></td>
	<td><?php echo $item->zip4; ?></td>
	<td><?php echo $item->address; ?></td>
	<td><?php echo $item->memo; ?></td>
	<td><?php echo $item->created_at; ?></td>
	<td><?php echo $item->updated_at; ?></td>
	<td><?php echo $item->deleted_at; ?></td>

		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>adrsが存在しません。</p>

<?php endif; ?>

