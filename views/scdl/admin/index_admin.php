<?php echo $search_form; ?>

<?php if ($items): ?>

<!--ページネーション-->
<div class="index_toolbar clearfix">
<?php echo \Pagination::create_links(); ?>
</div>

<!--一覧-->
<table class="tbl datatable tbl_scrollable lcm_focus" title="ユーザ一覧">
	<thead>
		<tr>
			<!--
			<th style="width: 10px; padding-right: 3px; padding-left: 3px;"><a role="button" class="button" style="padding: 4px 4px 2px; margin: 0;">選択</a></th>
			-->
			<th class="min"><?php echo \Pagination::sort('id', 'ID');?></th>
			<th><?php echo \Pagination::sort('title_text', 'タイトル');?></th>
			<th>期間</th>
			<th class="min"><?php echo \Pagination::sort('', '作成'); ?></th>
			<?php if (\Request::main()->action != 'index_deleted'): ?>
			<th class="min"><?php echo \Pagination::sort('', '更新'); ?></th>
			<?php endif; ?>
			<?php if (\Request::main()->action == 'index_deleted'): ?>
				<th>削除</th>
			<?php endif; ?>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php
	$ctrl = $locomo['controller']['name'] == '\Controller_Scdl_Admin' ? 'scdl' :'reserve/reserve' ; 
	$model_name = $ctrl == 'scdl' ? 'Locomo\Model_Scdl' : '\Reserve\Model_Reserve';
?>
<?php foreach ($items as $item): ?>
<?php
	$repeat_kbs = $model_name::get_repeat_kbs();
	$importance_kbs = $model_name::get_importance_kbs();
	$detail_kbs = $model_name::get_detail_kbs();
	$eventtitle_icon = '';
	//繰り返し区分
	$eventtitle_icon.= $item['repeat_kb'] != 0 ? '<span class="text_icon schedule repeat_kb_'.$item['repeat_kb'].'"></span>' : '';
	//詳細区分
	foreach($detail_kbs as $key => $value):
		if($item[$key]):
			$eventtitle_icon.= '<span class="text_icon schedule '.$key.'"></span>';
			$eventtitle_skip.= ' '.$value;
		endif;
	endforeach;
?>
		<tr class="l2" tabindex="-1" title="<?php echo $item->title_text ?>">
			<td class="ar"><?php echo $item->id; ?></td>
			<th><div class="col_scrollable" style="min-width: 12em;"><?php echo $eventtitle_icon.$item->title_text; ?></div></th>
			<td><?php echo $item->start_date.' '.$item->start_time.'〜<br>'.$item->end_date.' '.$item->end_time; ?></td>
			<td><?php echo $item->created_at.'<br>'.$item->create_user['display_name']; ?></td>
			<?php if (\Request::main()->action != 'index_deleted'): ?>
			<td><?php echo $item->updated_at.'<br>'.\Model_Usr::get_display_name($item->updater_id); ?></td>
			<?php endif; ?>
			<?php if (\Request::main()->action == 'index_deleted'): ?>
			<td><?php echo $item->deleted_at.'<br>'.\Model_Usr::get_display_name($item->updater_id); ?></td>
			<?php endif; ?>
			<td class="min">
				<div class="btn_group">
					<?php
					if (\Auth::is_admin()):
						echo Html::anchor($ctrl.'/viewdetail/'.$item->id, '<span class="skip">'.'を</span>閲覧', array('class' => 'view'));
						echo Html::anchor($ctrl.'/edit/'.$item->id, '編集', array('class' => 'edit'));
						if ($item->deleted_at):
							echo Html::anchor($ctrl.'/undelete/'.$item->id, '復活', array('class' => 'undelete confirm'));
							echo Html::anchor($ctrl.'/purge_confirm/'.$item->id, '完全に削除', array('class' => 'delete confirm'));
						else:
							echo Html::anchor($ctrl.'/delete/'.$item->id, '削除', array('class' => 'delete confirm'));
						endif;
					endif;
					?>
				</div>
			</td>
		</tr><?php endforeach; ?>
	</tbody>
	<tfoot class="thead">
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID');?></th>
			<th><?php echo \Pagination::sort('title_text', 'タイトル');?></th>
			<th>期間</th>
			<th><?php echo \Pagination::sort('', '作成'); ?></th>
			<?php if (\Request::main()->action != 'index_deleted'): ?>
			<th class="min"><?php echo \Pagination::sort('', '更新'); ?></th>
			<?php endif; ?>
			<?php if (\Request::main()->action == 'index_deleted'): ?>
				<th>削除</th>
			<?php endif; ?>
			<th>操作</th>
		</tr>
	</tfoot>
</table>
<?php include(LOCOMOPATH . "/views/scdl/inc_legend.php"); //カレンダ凡例 ?>
<?php endif; ?>
