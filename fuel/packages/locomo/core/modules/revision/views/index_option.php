<?php echo render('inc_header'); ?>

<?php if ($items): ?>
<table class="tbl2">
	<thead>
		<tr>
			<th class="ctrl">更新日時</th>
			<th>コメント</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr>
			<th style="white-space: nowrap;"><a href="<?php echo \Uri::base().$controller.'/option_revision/'.$optname.'/'.strtotime($item->created_at) ?>"><?php echo $item->created_at; ?></a></th>
			<td><?php echo $item->comment; ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>編集履歴が存在しません。</p>
<?php endif; ?>

<p><a href="<?php echo \Uri::base().$controller.'/option/'.$optname ?>" class="button">編集画面に戻る</a></p>

<?php echo render('inc_footer'); ?>
