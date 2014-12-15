<?php
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

<div class="index_wrapper">

<div class="left_column sub_column" style="width: 400px;">

<div class="form_group">
<table class="tbl">
<tr>
	<th><label for="searches[id]">顧客ID</label></th>
	<td><input type="text" name="nameStr" id="idStr" size="20" value="" /></td>
</tr>

<tr>
	<th><label for="likes[name]">顧客名</label></th>
	<td><input type="text" name="nameStr" id="idStr" size="20" value="" /></td>
</tr>

<tr>
	<th><label for="likes[kana]">顧客名カタカナ</label></th>
	<td><input type="text" name="nameStr" id="idStr" size="20" value="" /></td>
</tr>

<tr>
	<th><label for="user_type_1">ユーザー区分</label></th>
	<td>
		<select name="nameStr" id="idStr" style="width:90%;">
			<option value="foo" selected="selected">foo</option>
			<option value="bar">bar</option>
			<option value="baz">baz</option>
		</select>
		<select name="nameStr" id="idStr" style="width:90%;">
			<option value="foo" selected="selected">foo</option>
			<option value="bar">bar</option>
			<option value="baz">baz</option>
		</select>
	</td>
</tr>

<tr>
	<th><label for="dm_name">通信など</label></th>
	<td>
		<select name="nameStr" id="idStr" style="width:90%;">
			<option value="foo" selected="selected">foo</option>
			<option value="bar">bar</option>
			<option value="baz">baz</option>
		</select>
		<select name="nameStr" id="idStr" style="width:90%;">
			<option value="foo" selected="selected">foo</option>
			<option value="bar">bar</option>
			<option value="baz">baz</option>
		</select>
	</td>
</tr>

<tr>
	<th><label for="zip3_from">郵便番号</label></th>
	<td>
	<span class="nowrap">
		<input type="text" name="nameStr" id="idStr" size="3" placeholder="000" />&nbsp;-&nbsp;
		<input type="text" name="nameStr" id="idStr" size="4" placeholder="0000" />&nbsp;〜&nbsp;
	</span>
	<span class="nowrap">
		<input type="text" name="nameStr" id="idStr" size="3" placeholder="999" />&nbsp;-&nbsp;
		<input type="text" name="nameStr" id="idStr" size="4" placeholder="9999" />
	</span>
	</td>
</tr>

<tr>
	<th><label for="tel_num">電話番号</label></th>
	<td><input type="text" name="nameStr" id="idStr" size="20" value="" /></td>
</tr>

<tr>
	<th><label for="user_type_1">ボランティア保険</label></th>
	<td>
		<select name="nameStr" id="idStr" width="90%">
			<option value="foo" selected="selected">foo</option>
			<option value="bar">bar</option>
			<option value="baz">baz</option>
		</select>
	</td>
</tr>

<tr>
	<th><label for="is_death">死亡者を含める</label></th>
	<td><label><input type="checkbox" name="nameStr" id="is_death" value="" />is_death</label></td>
</tr>
</table>
<?php echo \Form::submit('submit', '検索', array('class' => 'primary button')); ?>
</div>
</div>
<?php 
//ここまで検索フォームとりあえず
 ?>



<!-- .right_column -->
<div class="right_column main_column index_table">

<?php if ($items): ?>
<table class="tbl datatable tbl_scrollable">
	<thead>
		<tr>
			<th><?php echo \Pagination::sort('id', 'ID', false);?></th>
			<th><?php echo \Pagination::sort('username', 'User name'); ?></th>
			<th><?php echo \Pagination::sort('email', 'Email'); ?></th>
			<th><?php echo \Pagination::sort('last_login_at', 'Last login'); ?></th>
			<th>Delete date</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($items as $item): ?>
		<tr tabindex="-1">
			<td><?php echo $item->id; ?></td>
			<td style="min-width: 6em;" ><div class="col_scrollable" tabindex="-1"><?php 
					echo Html::anchor('user/view'.'/'.$item->id, $item->display_name, array('class' => 'view'));?></div></td>
			<td style="min-width: 12em;"><div class="col_scrollable" tabindex="-1"><?php echo $item->email; ?></div></td>
			<td><?php echo $item->last_login_at; ?></td>
			<td><?php echo $item->deleted_at; ?></td>
			<td>
				<div class="btn_group">
					<?php
					echo Html::anchor('user/edit'.'/'.$item->id, '編集', array('class' => 'edit'));
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
</div><!-- /.right_column -->

</div><!-- /.index_wrapper -->