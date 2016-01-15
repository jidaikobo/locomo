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
<?php foreach ($items as $item):
	$msg_title = $item->name;
	$creator_name = \Model_Usr::get_display_name($item->creator_id);
	$create_time = date('Y年n月j日', strtotime($item->created_at));
?>
	<tr title="<?php echo $msg_title.'：'.$creator_name ?>" tabindex="-1">
		<th><div class="col_scrollable">
			<?php echo \Html::anchor(\Uri::create('msgbrd/view/'.$item->id), $msg_title.'<span class="skip"> 作成日 '.$create_time.' 投稿者 '.$creator_name.'</span>'); ?>
		</div></th>
		<td><?php echo $create_time ?>
		</td>
		<td><div class="col_scrollable" style="min-width: 4em;"><?php echo $creator_name; ?></div></td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>表示するメッセージがありません。</p>
<?php endif; ?>
