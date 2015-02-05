<h2>項目一覧<?php echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : ''; ?></h2>
<p><?php echo \Pagination::sort_info('\Schedules\Model_Schedules'); ?></p>
<?php 
	//index menu
	echo \Actionset::generate_menu_html($actionset['index'], array('class'=>'holizonal_list'));
?>
<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th><?php echo \Pagination::sort('repeat_kb', '繰り返し区分', false);?></th>
			<th><?php echo \Pagination::sort('week_kb', '繰り返し曜日', false);?></th>
			<th><?php echo \Pagination::sort('title_text', 'タイトル', false);?></th>
			<th><?php echo \Pagination::sort('title_kb', 'タイトル（区分）', false);?></th>
			<th><?php echo \Pagination::sort('message', 'メッセージ', false);?></th>
			<th><?php echo \Pagination::sort('group_kb', '表示するグループフラグ', false);?></th>
			<th><?php echo \Pagination::sort('group_detail', 'グループ指定', false);?></th>
			<th><?php echo \Pagination::sort('purpose_kb', '施設使用目的区分', false);?></th>
			<th><?php echo \Pagination::sort('purpose_text', '施設使用目的テキスト', false);?></th>
			<th><?php echo \Pagination::sort('user_num', '施設利用人数', false);?></th>
			<th><?php echo \Pagination::sort('user_id', '作成者', false);?></th>
			<th><?php echo \Pagination::sort('created_at', '作成日時', false);?></th>
			<th><?php echo \Pagination::sort('updated_at', '更新日時', false);?></th>
			<th><?php echo \Pagination::sort('deleted_at', '削除日', false);?></th>
			<th><?php echo \Pagination::sort('is_visible', '可視属性', false);?></th>

			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->repeat_kb; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->week_kb; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->title_text; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->title_kb; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->message; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->group_kb; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->group_detail; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->purpose_kb; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->purpose_text; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->user_num; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->user_id; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->created_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->updated_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->deleted_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->is_visible ? 'Yes' : 'No'; ?></div></td>
			<td>
				<div class="btn_group">
					<?php
					echo Html::anchor('scdl/viewdetail'.'/'.$item->id, '閲覧', array('class' => 'edit'));
					echo ' ';
					echo Html::anchor('scdl/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					?>
				</div>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>
<?php else: ?>
<p>schedulesが存在しません。</p>
<?php endif; ?>
