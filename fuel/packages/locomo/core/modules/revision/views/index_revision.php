<?php echo render('inc_header'); ?>

<?php if ($items): ?>

<?php
echo \Form::open(array('method' => 'get'));
echo \Form::input('likes[all]', \Input::get('likes.all'));
echo \Form::submit('submit', '検索', array('class' => 'button primary'));
echo \Form::close();
?>


<table class="tbl datatable">
	<thead>
		<tr>
			<th class="ctrl">ID</th>
			<th>最新表題</th>
			<th>操作</th>
			<th>編集者</th>
			<th>最新履歴日時</th>
			<th style="width:30%">コメント</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr>
			<th style="text-align:center;"><?php echo $item->pk_id ?></th>
			<th><div class="col_scrollable"><a href="<?php echo \Uri::base().$controller.'/each_index_revision/'.$model_simple_name.DS.$item->pk_id.$opt ?>"><?php echo $item->$subject; ?></a></div></th>
			<td><?php echo $item->operation; ?></td>
			<td><?php echo $item->modifier_name; ?></td>
			<td><?php echo $item->created_at; ?></td>
			<td><div class="col_scrollable"><?php echo $item->comment; ?></div></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>

<?php echo $pagination ?>

<?php else: ?>
<p>編集履歴が存在しません。</p>
<?php endif; ?>

<?php echo render('inc_footer'); ?>
