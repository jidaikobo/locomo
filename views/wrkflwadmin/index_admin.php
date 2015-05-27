<?php echo $search_form; ?>

<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
			<th class="ctrl"><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th style="text-align: left;"><?php echo \Pagination::sort('name', 'ワークフロー名'); ?></th>
			<th class="ctrl min">操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<td tabindex="0"><?php echo $item->id; ?></td>
			<td style="min-width: 6em;" ><div class="col_scrollable"><?php echo $item->name ;?></div></td>
			<td>
				<div class="btn_group">
					<?php
					if (\Auth::has_access('\Controller_Wrkflwadmin/view') && ! $item->deleted_at):
						echo '<span class="skip">ワークフロー</span>';
						echo Html::anchor('wrkflwadmin/setup'.'/'.$item->id, '<span class="skip">ID'.$item->id.' '.$item->name.'を</span>設定', array('class' => 'edit'));
					endif;
					if (\Auth::has_access('\Controller_Wrkflwadmin/edit')):
						echo Html::anchor('wrkflwadmin/edit/'.$item->id, '編集', array('class' => 'edit'));
					endif;
					if (\Auth::has_access('\Controller_Wrkflwadmin/delete')):
						if ($item->deleted_at):
							echo Html::anchor('wrkflwadmin/undelete/'.$item->id, '復活', array('class' => 'undelete confirm'));
							echo Html::anchor('wrkflwadmin/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
						else:
							echo Html::anchor('wrkflwadmin/delete/'.$item->id, '削除', array('class' => 'delete confirm'));
						endif;
					endif;
					?>
				</div>
			</td>
		</tr><?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>ワークフロー設定が存在しません</p>

<?php endif; ?>

