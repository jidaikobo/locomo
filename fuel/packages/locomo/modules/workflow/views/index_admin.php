<h2>項目一覧<?php echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : ''; ?></h2>
<p><?php echo \Pagination::sort_info('\User\Model_User'); ?></p>
<?php
	//index menu
	echo \Actionset::generate_menu_html($actionset['index'], array('class'=>'holizonal_list'));
?>
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
			<td><?php echo $item->id; ?></td>
			<td style="min-width: 6em;" ><div class="col_scrollable" tabindex="-1"><?php 
					echo $item->name ;?></div></td>
			<td>
				<div class="btn_group">
					<?php
					echo Html::anchor('workflow/workflowadmin/setup'.'/'.$item->id, '設定', array('class' => 'edit'));
					echo Html::anchor('workflow/workflowadmin/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					?>
				</div>
			</td>
		</tr><?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>

<?php else: ?>
<p>ユーザが存在しません。</p>

<?php endif; ?>
