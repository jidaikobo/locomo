<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus" title="項目一覧">
	<thead>
		<tr>
			<th>名称</th>
			<th>説明</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($items as $item): ?>
	<?php if (\Controller_Flr::check_auth($item->path)): ?>
		<tr title="<?php echo $item->genre.' '.$item->name.' (登録日:'.date('Y年n月j日G時i分', strtotime($item->created_at)).')' ?>" tabindex="-1">
			<td><div class="col_scrollable" style="min-width: 12em;">
			<?php
				if ($item->genre == 'dir'):
					echo Html::anchor('flr/index_files'.DS.$item->id, $item->name, array('class' => 'icon dir'));
				else:
					echo Html::anchor('flr/file/dl/?dl=1&p='.\Model_Flr::enc_url($item->path, true), $item->name, array('class' => 'icon '.$item->genre));
				endif;
			?>
			</div></td>
			<td><div class="col_scrollable"><?php echo $item->explanation; ?></div></td>
		</tr>
	<?php endif; ?>
	<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>ファイルが存在しません。<a href="<?php echo \Uri::create('/flr/index_files/') ?>">ファイラ</a>でファイルをアップして、「ダッシュボードに表示」をチェックしてください。</p>
<?php endif; ?>
