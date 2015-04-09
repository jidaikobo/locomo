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
			<th class="min">担当者</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<th><div class="col_scrollable" style="min-width: 10em;">
			<?php
				if ($item->genre == 'dir'):
					echo Html::anchor('flr/index_files'.DS.$item->id, $item->name, array('class' => 'icon dir'));
				else:
					if (\Controller_Flr::check_auth($item->path, 'read')):
						echo Html::anchor('flr/file/view'.DS.$item->id, $item->name, array('class' => 'icon '.$item->genre));
					else:
						echo '<span class="icon '.$item->genre.'">'.$item->name.'</span>';
					endif;
				endif;
			?>
			</div></th>
<?php if (\Input::get('submit')): ?>
			<td><div class="col_scrollable" style="min-width: 6em;"><?php echo dirname(urldecode($item->path)) ?></div></td>
<?php endif; ?>
			<td><?php
				if ($item->genre !== 'dir' && \Controller_Flr::check_auth($item->path, 'read')):
					echo \Html::anchor(\Uri::create('flr/file/dl/?p='.\Model_Flr::enc_url($item->path, true)), 'ダウンロード', array('class' => 'button small'));
				endif;
			?></td>
			<td><?php echo $item->genre; ?></td>
			<td><div class="col_scrollable" style="min-width: 6em;"><?php echo $item->explanation; ?></div></td>
			<td><?php echo \Model_Usr::get_display_name($item->creator_id); ?></td>
		</tr><?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>ファイルおよびディレクトリが存在しません。</p>

<?php endif; ?>
