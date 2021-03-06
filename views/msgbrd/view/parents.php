<h2>メッセージ<?php echo $item->name ? '：'.$item->name : '' ?></h2>
<table class="tbl">
<?php
	// use model's form plain definition instead of raw-like html
	//echo $plain;
?>
<?php if ($item->contents): ?>
<tr>
	<th>本文</th>
	<td><?php echo nl2br($item->contents); ?></td>
</tr>

<?php endif; ?>
<?php if ($item->is_draft): ?>
<tr>
	<th>下書き</th>
	<td><?php echo $item->is_draft ? 'Yes' : 'No'; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->category_id): ?>
<tr>
	<th>カテゴリ</th>
	<td><?php echo $item->category_id; ?></td>
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
<?php if ($item->expired_at): ?>
<tr>
	<th>有効期日</th>
	<td><?php echo $item->expired_at; ?></td>
</tr>

<?php endif; ?>
<?php if ($item->deleted_at): ?>
<tr>
	<th>削除日</th>
	<td><?php echo $item->deleted_at; ?></td>
</tr>

<?php endif; ?>
<tr>
	<th>作成者</th>
	<td><?php echo \Model_Usr::get_display_name($item->creator_id) ?></td>
</tr>
</table>

