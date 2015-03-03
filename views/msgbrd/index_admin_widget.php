<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
			<th>表題</th>
			<th class="min">作成日</th>
			<th>投稿者</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
	<tr title="<?php echo $item->name.'：'.\Model_Usr::get_display_name($item->creator_id) ?>" tabindex="-1">
		<td><div class="col_scrollable">
				<?php echo \Html::anchor(\Uri::create('msgbrd/view/'.$item->id), $item->name); ?>
		</div></td>
		<td><?php echo date('Y年n月j日', strtotime($item->created_at)) ?>
		</td>
		<td><div class="col_scrollable" style="min-width: 4em;"><?php echo \Model_Usr::get_display_name($item->creator_id); ?></div></td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>msgbrdが存在しません。</p>
<?php endif; ?>
