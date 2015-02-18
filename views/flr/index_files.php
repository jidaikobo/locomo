<h1><?php echo $search_form ?></h1>

<?php if ( ! \Input::get('submit')) echo $breadcrumbs ;?>

<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus" title="ファイル一覧">
	<thead>
		<tr>
			<th>名称</th>
<?php if (\Input::get('submit')): ?>
			<th>パス</th>
<?php endif; ?>
			<th class="min">操作</th>
			<th>種類</th>
			<th>説明</th>
			<th>担当者</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<th style="min-width: 6em;" ><div class="col_scrollable" tabindex="-1" style="min-width: 12em;">
			<?php
				if ($item->genre == 'dir'):
					echo Html::anchor('flr/index_files'.DS.$item->id, $item->name, array('class' => 'icon dir'));
				else:
					echo Html::anchor('flr/file/view'.DS.$item->id, $item->name, array('class' => 'icon '.$item->genre));
				endif;
			?>
			</div></th>
<?php if (\Input::get('submit')): ?>
			<td><div class="col_scrollable" tabindex="-1"><?php echo dirname(urldecode($item->path)) ?></div></td>
<?php endif; ?>
			<td><?php
				if ($item->genre !== 'dir'):
					echo \Html::anchor(\Uri::create('flr/file/dl/?p='.\Model_Flr::enc_url($item->path, true)), 'ダウンロード', array('class' => 'button small'));
				endif;
			?></td>
			<td><?php echo $item->genre; ?></td>
			<td><?php echo $item->explanation; ?></td>
			<td><?php echo \Model_Usr::get_display_name($item->creator_id); ?></td>
		</tr><?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>ファイルおよびディレクトリが存在しません。</p>

<?php endif; ?>
