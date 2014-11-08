<h2>項目一覧<?php echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : ''; ?></h2>
<p><?php echo \Pagination::sort_info('\User\Model_User'); ?></p>
<?php 
	//index menu
	echo \Actionset::generate_menu_html($actions['index'], array('class'=>'holizonal_list'));
?>
<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th><?php echo \Pagination::sort('title', '表題', false);?></th>
			<th><?php echo \Pagination::sort('body', '本文', false);?></th>
			<th><?php echo \Pagination::sort('is_bool', '真偽値', false);?></th>
			<th><?php echo \Pagination::sort('created_at', '作成日時', false);?></th>
			<th><?php echo \Pagination::sort('updated_at', '更新日時', false);?></th>
			<th><?php echo \Pagination::sort('expired_at', '有効期日', false);?></th>
			<th><?php echo \Pagination::sort('deleted_at', '削除日', false);?></th>
			<th><?php echo \Pagination::sort('is_visible', '可視属性', false);?></th>

			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->title; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->body; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->is_bool ? 'Yes' : 'No' ; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->created_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->updated_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->expired_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->deleted_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->is_visible ? 'Yes' : 'No' ; ?></div></td>
			<td>
				<div class="btn_group">
					<?php
					echo Html::anchor('test1/view'.'/'.$item->id, '閲覧', array('class' => 'edit'));
					echo ' ';
					echo Html::anchor('test1/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					?>
				</div>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>
<?php else: ?>
<p>test1が存在しません。</p>
<?php endif; ?>

