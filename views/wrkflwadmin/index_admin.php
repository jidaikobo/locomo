<?php echo $search_form; ?>

<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
			<th class="ctrl"><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th style="text-align: left;"><?php echo \Pagination::sort('name', 'ワークフロー名'); ?></th>
			<th class="ctrl">操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<td tabindex="0"><?php echo $item->id; ?></td>
			<td style="min-width: 6em;" ><div class="col_scrollable" tabindex="0"><?php echo $item->name ;?></div></td>
			<td>
				<div class="btn_group">
					<?php
					echo Html::anchor('wrkflwadmin/view'.'/'.$item->id, '表示', array('class' => 'view'));
					if ( ! $item->deleted_at):
						echo Html::anchor('wrkflwadmin/setup'.'/'.$item->id, '設定', array('class' => 'edit'));
					endif;
					echo Html::anchor('wrkflwadmin/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					?>
				</div>
			</td>
		</tr><?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>ワークフロー設定が存在しません。</p>

<?php endif; ?>

