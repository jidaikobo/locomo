<?php
	// index menu
	echo \Actionset::generate_menu_html($actionset['index'], array('class'=>'holizonal_list'));
	// index information
	echo '<p class="index_info">';
	echo \Pagination::sort_info('\User\Model_Workfrowadmin');
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
					echo Html::anchor('workflow/workflowadmin/view'.'/'.$item->id, '表示', array('class' => 'view'));
					if ( ! $item->deleted_at):
						echo Html::anchor('workflow/workflowadmin/setup'.'/'.$item->id, '設定', array('class' => 'edit'));
					endif;
					echo Html::anchor('workflow/workflowadmin/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
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

