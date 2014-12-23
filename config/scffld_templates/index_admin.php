<?php
	// index menu
	echo \Actionset::generate_menu_html($actionset['index'], array('class'=>'holizonal_list'));
	// index information
	echo '<p class="index_info">';
	echo \Pagination::sort_info('\User\Model_XXX');
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
###THEAD###
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>		<tr>
###TBODY###			<td>
				<div class="btn_group">
					<?php
					echo Html::anchor('xxx/view'.'/'.$item->id, '閲覧', array('class' => 'edit'));
					echo ' ';
					echo Html::anchor('xxx/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
					?>
				</div>
			</td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php echo \Pagination::create_links(); ?>
<?php else: ?>
<p>xxxが存在しません。</p>
<?php endif; ?>
