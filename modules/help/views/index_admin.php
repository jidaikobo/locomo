<?php
	if(isset($default_help)):
		echo $default_help;
	endif;


	// index menu
	echo \Actionset::generate_menu_html($actionset['index'], array('class'=>'holizonal_list'));
	// index information
	echo '<p class="index_info">';
	echo \Pagination::sort_info('\User\Model_User');
	echo (\Pagination::get('total_items') != 0) ? '（全'.\Pagination::get('total_items').'件）' : '';
	echo '</p>';

	// search form
	echo \Form::open(array('method' => 'get', 'class' => 'index_search_form'));
	echo \Form::input(array('name' => 'all', 'type' => 'text', 'value' => \Input::get('all') ?: '',));
	echo \Form::submit('submit', '検索', array('class' => 'button primary'));
	echo \Form::close();
?>
<?php if ($items): ?>
<table class="tbl datatable">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th><?php echo \Pagination::sort('title', '表題', false);?></th>
			<th><?php echo \Pagination::sort('mod_or_ctrl', 'コントローラ', false);?></th>
			<th><?php echo \Pagination::sort('body', '本文', false);?></th>
			<th><?php echo \Pagination::sort('updated_at', '更新日時', false);?></th>
			<th><?php echo \Pagination::sort('deleted_at', '削除日', false);?></th>

			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
	<td><?php echo $item->id; ?></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->title; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->mod_or_ctrl; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->body; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->updated_at; ?></div></td>
	<td><div class="col_scrollable" tabindex="-1"><?php echo $item->deleted_at; ?></div></td>
			<td>
				<div class="btn_group">
					<?php
					echo Html::anchor('help/view'.'/'.$item->id, '閲覧', array('class' => 'edit'));
					echo ' ';
					echo Html::anchor('help/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					?>
				</div>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>
<?php else: ?>
<p>helpが存在しません。</p>
<?php endif; ?>