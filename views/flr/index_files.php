<h1>現在のディレクトリ：<?php echo $current->name ?></h1>
<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus">
	<thead>
		<tr>
			<th>名称</th>
			<th>種類</th>
			<th>説明</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<td style="min-width: 6em;" ><div class="col_scrollable" tabindex="-1">
			<?php
				if ($item->genre == 'dir'):
					echo Html::anchor('flr/index_files'.DS.$item->id, $item->name, array('class' => 'icon'));
				else:
					echo Html::anchor('flr/view_file'.DS.$item->id, $item->name, array('class' => 'icon'));
				endif;
			?>
			</div></td>
			<td><?php echo $item->genre; ?></td>
			<td><?php echo $item->explanation; ?></td>
		</tr><?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>ファイルおよびディレクトリが存在しません。</p>

<?php endif; ?>
