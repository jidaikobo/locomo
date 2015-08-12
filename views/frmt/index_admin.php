<?php echo $search_form ?>

<div class="main_column index_table">
<?php if ($items): ?>

<table class="tbl datatable tbl_scrollable lcm_focus" title="項目一覧">
	<thead>
		<tr>
			<th class="min">ID</th>
			<th class="min">表示順</th>
			<th class="min">フォーマット名</th>
			<th class="min">操作</th>
			<?php if ($output_url) { ?><th class="min">テスト印刷</th><?php } ?>
		</tr>
	</thead>
	<tbody class="has_checkbox">
<?php foreach ($items as $item): ?>
		<tr>
			<td class="min ar"><?php echo $item->id; ?></td>
			<td class="min ar"><?php echo $item->seq; ?></td>
			<td class="min"><span class="icon <?php echo ($item->type == 'excel') ? 'xls' : $item->type ; ?>"><?php echo $item->name; ?></span></td>
			<td class="min"><?php echo \Locomo\Presenter_Frmt_Index_Admin::create_ctrls($item); ?></td>
			<?php if ($output_url) : ?>
				<td class="min"><?php echo \Locomo\Presenter_Frmt_Index_Admin::create_preview($item, $output_url); ?></td>
			<?php endif; ?>
		</tr>
<?php endforeach; ?>
	</tbody>
<!-- // TODO 後で上に合わせる
	<tfoot class="thead">
		<tr>
			<th class="min">ID</th>
			<th class="min">name</th>
			<th class="min">edit</th>
		</tr>
	</tfoot>
-->
</table>
<?php else: ?>
<?php endif; ?>
</div><!-- /.index_table -->
