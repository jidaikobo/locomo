<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable lcm_focus">
	<thead>
		<tr>
			<th>名称</th>
			<th>説明</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($items as $item): ?>
	<?php if (\Controller_Flr::check_auth($item->path)): ?>
		<tr tabindex="-1">
			<td style="min-width: 6em;"><div class="col_scrollable" tabindex="-1">
			<?php
				if ($item->genre == 'dir'):
					echo Html::anchor('flr/index_files'.DS.$item->id, $item->name, array('class' => 'icon'));
				else:
					echo Html::anchor('flr/dl/?dl=1&p='.\Model_Flr::enc_url($item->path, true), $item->name, array('class' => 'icon'));
				endif;
			?>
			</div></td>
			<td><?php echo $item->explanation; ?></td>
		</tr>
	<?php endif; ?>
	<?php endforeach; ?>
	</tbody>
</table>

<?php else: ?>
<p>ファイルが存在しません。<a href="<?php echo \Uri::create('/flr/index_files/') ?>">ファイラ</a>でファイルをアップして、「ダッシュボードに表示」をチェックしてください。</p>
<?php endif; ?>
