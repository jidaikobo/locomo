<h1><?php echo $search_form ?></h1>

<?php if ( ! \Input::get('submit')) echo $breadcrumbs ;?>

<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus">
	<thead>
		<tr>
			<th>名称</th>
<?php if (\Input::get('submit')): ?>
			<th>パス</th>
<?php endif; ?>
			<th>種類</th>
			<th>説明</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<th style="min-width: 6em;" ><div class="col_scrollable" tabindex="-1">
			<?php
				if ($item->genre == 'dir'):
					echo Html::anchor('flr/index_files'.DS.$item->id, $item->name, array('class' => 'icon'));
				else:
					echo Html::anchor('flr/view_file'.DS.$item->id, $item->name, array('class' => 'icon'));
				endif;
			?>
			</div></th>
<?php if (\Input::get('submit')): ?>
			<td><div class="col_scrollable" tabindex="-1"><?php echo urldecode($item->path) ?></div></td>
<?php endif; ?>
			<td><?php echo $item->genre; ?></td>
			<td><?php echo $item->explanation; ?></td>
		</tr><?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>ファイルおよびディレクトリが存在しません。</p>

<?php endif; ?>
