<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
			<th>表題</th>
			<th>投稿者</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
	<tr>
		<td><div class="col_scrollable" tabindex="-1"><?php echo \Html::anchor(\Uri::create('msgbrd/view/'.$item->id), $item->name); ?></div></td>
		<td><div class="col_scrollable" tabindex="-1"><?php echo \Model_Usr::get_display_name($item->creator_id); ?></div></td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>msgbrdが存在しません。</p>
<?php endif; ?>
