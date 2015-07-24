<?php echo $search_form; ?>

<?php if ($items): ?>

<!--ページネーション-->
<div class="index_toolbar clearfix">
<?php echo \Pagination::create_links(); ?>
</div>

<!--一覧-->
<table class="tbl datatable">
	<thead>
		<tr>
			<th class="min"><?php echo \Pagination::sort('group.name', 'グループ', false);?></th>
			<th style="width: 10em;"><?php echo \Pagination::sort('kana', '氏名', false);?></th>
<!--			<th><?php echo \Pagination::sort('kana', 'かな', false);?></th>-->
			<th><?php echo \Pagination::sort('tel', '電話番号', false);?></th>
<!--			<th><?php echo \Pagination::sort('fax', 'FAX番号', false);?></th>-->
<!--			<th><?php echo \Pagination::sort('mail', 'メールアドレス', false);?></th>-->
<!--			<th><?php echo \Pagination::sort('mobile', '携帯電話', false);?></th>-->
			<th class="nd_if_smalldisplay" style="width: 5em;"><?php echo \Pagination::sort('zip3', '郵便番号', false);?></th>
			<th class="nd_if_smalldisplay"><?php echo \Pagination::sort('address', '住所', false);?></th>
<!--			<th><?php echo \Pagination::sort('memo', '備考', false);?></th>-->
			<th class="min">操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr>
	<td><?php echo $item->group['name']; ?></td>
	<td><div class="col_scrollable" style="min-width: 4em;"><?php echo $item->name; ?></div></td>
<!--	<td><div class="col_scrollable"><?php echo $item->kana; ?></div></td>-->
	<td><div class="col_scrollable" style="min-width: 8em;"><?php echo $item->tel; ?></div></td>
<!--	<td><div class="col_scrollable"><?php echo $item->fax; ?></div></td>-->
<!--	<td><div class="col_scrollable"><?php echo $item->mail; ?></div></td>-->
<!--	<td><div class="col_scrollable"><?php echo $item->mobile; ?></div></td>-->
	<td class="nd_if_smalldisplay"><?php
		$item->zip3.= $item->zip3 ? '-' : '';
		echo $item->zip3 . $item->zip4;
	?></td>
	<td class="nd_if_smalldisplay"><div class="col_scrollable" style="min-width: 4em;"><?php echo $item->address; ?></div></td>
<!--	<td><div class="col_scrollable"><?php echo $item->memo; ?></div></td>-->
			<td>
				<div class="btn_group">
					<?php
					if (\Auth::has_access('\Controller_Adrs/view')):
						echo Html::anchor('adrs/view/'.$item->id, '<span class="skip">'.$item->name.'を</span>閲覧', array('class' => 'view'));
					endif;
					if (\Auth::has_access('\Controller_Adrs/edit')):
						echo Html::anchor('adrs/edit/'.$item->id, '編集', array('class' => 'edit'));
					endif;
					if (\Auth::has_access('\Controller_Adrs/delete')):
						if ($item->deleted_at):
							echo Html::anchor('adrs/undelete/'.$item->id, '復活', array('class' => 'undelete confirm'));
							echo Html::anchor('adrs/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
						else:
							echo Html::anchor('adrs/delete/'.$item->id, '削除', array('class' => 'delete confirm'));
						endif;
					endif;
					?>
				</div>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
