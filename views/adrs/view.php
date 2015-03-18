<table class="tbl">
<?php
	// use model's form plain definition instead of raw-like html
	//echo $plain;
?>
<?php if ($item->name): ?>
<tr>
	<th>氏名</th>
	<td><?php echo $item->name; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->kana): ?>
<tr>
	<th>かな</th>
	<td><?php echo $item->kana; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->company_name): ?>
<tr>
	<th>会社名</th>
	<td><?php echo $item->company_name; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->company_kana): ?>
<tr>
	<th>会社名かな</th>
	<td><?php echo $item->company_kana; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->tel): ?>
<tr>
	<th>電話番号</th>
	<td><?php echo $item->tel; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->fax): ?>
<tr>
	<th>FAX番号</th>
	<td><?php echo $item->fax; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->mail): ?>
<tr>
	<th>メールアドレス</th>
	<td><?php echo $item->mail; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->mobile): ?>
<tr>
	<th>携帯電話</th>
	<td><?php echo $item->mobile; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->zip3): ?>
<tr>
	<th>郵便番号</th>
	<td><?php echo $item->zip3.'-'.$item->zip4; ?></td>
</tr>

<?php endif; ?>

<?php if ($item->address): ?>
<tr>
	<th>住所</th>
	<td><?php echo $item->address; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->memo): ?>
<tr>
	<th>備考</th>
	<td><?php echo nl2br($item->memo); ?></td>
</tr>

<?php endif; ?>
<?php if ($item->created_at): ?>
<tr>
	<th>作成日時</th>
	<td><?php echo $item->created_at; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->updated_at): ?>
<tr>
	<th>更新日時</th>
	<td><?php echo $item->updated_at; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->deleted_at): ?>
<tr>
	<th>削除日</th>
	<td><?php echo $item->deleted_at; ?></td>
</tr>

<?php endif; ?>

</table>
