<?php echo $search_form; ?>

<?php if ($items): ?>

<!--.index_toolbar-->
<div class="index_toolbar clearfix">
<!--.index_toolbar_buttons-->
<div class="index_toolbar_buttons">
</div><!-- /.index_toolbar_buttons -->
<?php echo \Pagination::create_links(); ?>
</div><!-- /.index_toolbar -->

<table class="tbl">
	<thead>
		<tr>
			<th class="ar min"><?php echo \Pagination::sort('id', 'ID', false);?></th>

		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>

		</tr>
<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<th class="ar min"><?php echo \Pagination::sort('id', 'ID', false);?></th>

		</tr>
	</tfoot>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>srchが存在しません。</p>

<?php endif; ?>
