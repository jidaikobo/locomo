<?php echo $search_form ?>

<div class="main_column index_table">
<?php if ($items): ?>


<?php
$index_toolbar = '<div class="index_toolbar clearfix">';
$index_toolbar.= '<div class="index_toolbar_buttons">';
$index_toolbar.= '</div> <!-- /.index_toolbar_buttons -->';
$index_toolbar.= \Pagination::create_links();
$index_toolbar.= '</div> <!-- /.index_toolbar -->';
?>

<?php echo $index_toolbar; ?>

<table class="tbl datatable tbl_scrollable lcm_focus" title="項目一覧">
	<thead>
		<tr>
			<th class="min">ID</th>
			<th class="min">表示順</th>
			<th class="min">使用</th>
			<th class="min">要素数</th>
			<th class="min">操作</th>
		</tr>
	</thead>
	<tbody class="has_checkbox">
<?php foreach ($items as $item): ?>
		<tr>
			<td class="min ar"><?php echo $item->id; ?></td>
			<td class="min ar"><?php echo $item->seq; ?></td>
			<td class="min ac"><?php echo $item->is_draft ? '<span style="color:IndianRed;">下書き</span>' : '<span style="color:green;">使用中</span>'; ?></td>
			<td class="min ac">
				<?php echo count($item->element); ?>
			</td>
			<td class="min"><?php echo \Locomo\Presenter_Frmt_Table_Index::create_ctrls($item); ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
	<tfoot class="thead">
			<th class="min">ID</th>
			<th class="min">表示順</th>
			<th class="min">使用</th>
			<th class="min">要素数</th>
			<th class="min">操作</th>
	</tfoot>
</table>

<?php echo $index_toolbar; ?>

<?php else: ?>

<p>該当のフォーマットがありません。</p>

<?php endif; ?>

</div><!-- /.index_table -->
